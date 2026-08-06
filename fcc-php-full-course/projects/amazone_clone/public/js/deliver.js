// deliver.js — full-size "Manage your delivery locations" map page.
// Reads/writes the same localStorage keys as the header popover (main.js).
(function () {
  const LIST_KEY = "amazone_saved_addresses";
  const ACTIVE_KEY = "amazone_deliver_to";

  const page = document.querySelector(".amz-map-page");
  if (!page) return;

  const mapsKey = page.dataset.mapsKey || "";
  const input = document.getElementById("map-search-input");
  const locateBtn = document.getElementById("map-locate");
  const canvas = document.getElementById("map-canvas");
  const readoutLabel = document.getElementById("map-readout-label");
  const deliverBtn = document.getElementById("map-deliver-here");
  const listEl = document.getElementById("saved-addresses");
  const emptyEl = document.getElementById("saved-empty");

  let map = null;
  let marker = null;
  let selection = null; // { label, lat, lng } currently picked on the map/search

  function readList() {
    try {
      const v = JSON.parse(localStorage.getItem(LIST_KEY));
      return Array.isArray(v) ? v : [];
    } catch {
      return [];
    }
  }

  function writeList(list) {
    localStorage.setItem(LIST_KEY, JSON.stringify(list));
  }

  function readActive() {
    try {
      return JSON.parse(localStorage.getItem(ACTIVE_KEY));
    } catch {
      return null;
    }
  }

  function setActive(loc) {
    if (loc) {
      localStorage.setItem(ACTIVE_KEY, JSON.stringify({ label: loc.label, lat: loc.lat, lng: loc.lng }));
    } else {
      localStorage.removeItem(ACTIVE_KEY);
    }
    const headerName = document.getElementById("deliver-name");
    if (headerName) headerName.textContent = loc ? loc.label : "Your Home";
  }

  function addToSaved(loc) {
    const list = readList();
    if (!list.some((a) => a.label === loc.label)) {
      list.push({ label: loc.label, lat: loc.lat, lng: loc.lng });
      writeList(list);
    }
  }

  function renderList() {
    const list = readList();
    const active = readActive();
    emptyEl.hidden = list.length > 0;
    listEl.textContent = "";

    list.forEach((loc, i) => {
      const li = document.createElement("li");
      li.className = "amz-address-item";
      if (active && active.label === loc.label) li.classList.add("active");

      const info = document.createElement("div");
      info.className = "amz-address-info";

      const label = document.createElement("strong");
      label.textContent = "📌 " + loc.label;
      info.appendChild(label);

      if (typeof loc.lat === "number" && typeof loc.lng === "number") {
        const coords = document.createElement("span");
        coords.className = "amz-address-coords";
        coords.textContent = loc.lat.toFixed(4) + ", " + loc.lng.toFixed(4);
        info.appendChild(coords);
      }
      li.appendChild(info);

      const actions = document.createElement("div");
      actions.className = "amz-address-actions";

      if (!(active && active.label === loc.label)) {
        const here = document.createElement("button");
        here.type = "button";
        here.textContent = "Deliver here";
        here.className = "amz-btn amz-btn-sm";
        here.addEventListener("click", () => { setActive(loc); renderList(); });
        actions.appendChild(here);
      } else {
        const badge = document.createElement("span");
        badge.className = "amz-address-active";
        badge.textContent = "Delivering to this address";
        actions.appendChild(badge);
      }

      const href = (typeof loc.lat === "number")
        ? "https://www.google.com/maps/search/?api=1&query=" + loc.lat + "," + loc.lng
        : "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(loc.label);
      const open = document.createElement("a");
      open.href = href;
      open.target = "_blank";
      open.rel = "noopener";
      open.textContent = "Open in Maps ↗";
      actions.appendChild(open);

      const remove = document.createElement("button");
      remove.type = "button";
      remove.className = "amz-address-remove";
      remove.textContent = "Remove";
      remove.addEventListener("click", () => {
        const list2 = readList();
        list2.splice(i, 1);
        writeList(list2);
        if (active && active.label === loc.label) setActive(null);
        renderList();
      });
      actions.appendChild(remove);

      li.appendChild(actions);
      listEl.appendChild(li);
    });
  }

  function select(loc, focusMap) {
    selection = loc;
    readoutLabel.textContent = "📌 " + loc.label;
    deliverBtn.disabled = false;
    if (marker && map && focusMap) {
      const pos = { lat: loc.lat, lng: loc.lng };
      marker.setPosition(pos);
      map.setCenter(pos);
      map.setZoom(13);
    }
  }

  deliverBtn.addEventListener("click", () => {
    if (!selection) return;
    addToSaved(selection);
    setActive(selection);
    renderList();
    readoutLabel.textContent = "Delivering to: " + selection.label;
    deliverBtn.disabled = true;
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && (input.value || "").trim()) {
      e.preventDefault();
      select({ label: input.value.trim() }, true);
    }
  });

  // Browser geolocation + reverse geocode (needs key for a nice label).
  locateBtn.addEventListener("click", () => {
    if (!navigator.geolocation) {
      readoutLabel.textContent = "Geolocation isn't supported here.";
      return;
    }
    locateBtn.disabled = true;
    locateBtn.textContent = "Locating…";
    navigator.geolocation.getCurrentPosition(async (pos) => {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;
      let label = "Current location";
      if (mapsKey) {
        try { label = await reverseGeocode(lat, lng); } catch { /* generic */ }
      }
      const loc = { label, lat, lng };
      select(loc, true);
      addToSaved(loc);
      setActive(loc);
      renderList();
      readoutLabel.textContent = "Delivering to: " + label;
      deliverBtn.disabled = true;
    }, () => {
      readoutLabel.textContent = "Location permission denied.";
    }).then(() => {
      locateBtn.disabled = false;
      locateBtn.textContent = "📌 Use my location";
    }).catch(() => {
      locateBtn.disabled = false;
      locateBtn.textContent = "📌 Use my location";
    });
  });

  async function reverseGeocode(lat, lng) {
    const res = await fetch("https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + lng + "&key=" + encodeURIComponent(mapsKey));
    const data = await res.json();
    if (data.results && data.results[0]) return data.results[0].formatted_address;
    return lat.toFixed(4) + ", " + lng.toFixed(4);
  }

  // Load Google Maps lazily, then wire search + map.
  function loadMaps() {
    if (!mapsKey || window.google) return;
    const script = document.createElement("script");
    script.src = "https://maps.googleapis.com/maps/api/js?key=" + encodeURIComponent(mapsKey) + "&libraries=places,marker&loading=async";
    script.async = true;
    script.onload = () => { initAutocomplete(); initMap(); };
    document.head.appendChild(script);
  }

  function initAutocomplete() {
    if (!window.google || !window.google.maps) return;
    const ac = new google.maps.places.Autocomplete(input, { types: ["geocode"] });
    ac.addListener("place_changed", () => {
      const place = ac.getPlace();
      if (!place.geometry) return;
      const loc = place.geometry.location;
      select({ label: place.formatted_address || input.value, lat: loc.lat(), lng: loc.lng() }, true);
    });
  }

  function initMap() {
    if (!window.google || !window.google.maps.Map) return;
    const active = readActive();
    const center = (active && typeof active.lat === "number")
      ? { lat: active.lat, lng: active.lng }
      : { lat: 47.6062, lng: -122.3321 }; // Amazon HQ — default "Your Home"

    map = new google.maps.Map(canvas, {
      center,
      zoom: 12,
      mapTypeControl: false,
      fullscreenControl: false
    });
    marker = new google.maps.Marker({ map, position: center, draggable: true });

    google.maps.event.addListener(map, "click", async (e) => {
      const lat = e.latLng.lat();
      const lng = e.latLng.lng();
      let label;
      try { label = await reverseGeocode(lat, lng); } catch { label = lat.toFixed(4) + ", " + lng.toFixed(4); }
      select({ label, lat, lng }, false);
    });
    google.maps.event.addListener(marker, "dragend", async () => {
      const pos = marker.getPosition();
      const lat = pos.lat();
      const lng = pos.lng();
      let label;
      try { label = await reverseGeocode(lat, lng); } catch { label = lat.toFixed(4) + ", " + lng.toFixed(4); }
      select({ label, lat, lng }, false);
    });
  }

  renderList();
  loadMaps();
})();
