// main.js — Amazon-style interactions (no libraries).
document.addEventListener("DOMContentLoaded", () => {
  const menu = document.getElementById("nav-menu");
  const toggle = document.getElementById("nav-toggle");

  // Mobile: click "All" toggles the departments dropdown.
  if (toggle && menu) {
    toggle.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.classList.toggle("open");
      toggle.setAttribute("aria-expanded", menu.classList.contains("open"));
    });
    document.addEventListener("click", () => menu.classList.remove("open"));
    menu.addEventListener("click", (e) => e.stopPropagation());
  }

  // Quantity stepper on the product page.
  const minus = document.getElementById("qty-minus");
  const plus = document.getElementById("qty-plus");
  const qtyVal = document.getElementById("qty-val");
  if (minus && plus && qtyVal) {
    let qty = 1;
    minus.addEventListener("click", () => { if (qty > 1) { qty--; qtyVal.textContent = qty; } });
    plus.addEventListener("click", () => { if (qty < 99) { qty++; qtyVal.textContent = qty; } });
  }

  const cartBadge = document.getElementById("cart-count");
  const csrfToken = () => (document.querySelector("meta[name=csrf]") || {}).content || "";

  // Real "Add to Cart" — POSTs to /cart/add, updates the badge from the response.
  document.querySelectorAll("[data-add-to-cart]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      btn.disabled = true;
      const original = btn.textContent;
      btn.textContent = "Adding…";
      const qty = qtyVal ? parseInt(qtyVal.textContent, 10) || 1 : 1;

      try {
        const res = await fetch("/cart/add", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            csrf_token: csrfToken(),
            product_id: btn.dataset.addToCart,
            quantity: qty
          })
        });
        const data = await res.json();
        if (cartBadge && typeof data.count === "number") {
          cartBadge.textContent = data.count;
          cartBadge.classList.remove("bump");
          void cartBadge.offsetWidth;
          cartBadge.classList.add("bump");
        }
        btn.textContent = "Added to cart";
      } catch {
        btn.textContent = "Couldn't add";
      }

      setTimeout(() => { btn.textContent = original; btn.disabled = false; }, 1400);
    });
  });

  // "Buy Now" jumps to the cart.
  document.querySelectorAll("[data-buy-now]").forEach((btn) => {
    btn.addEventListener("click", () => { window.location.href = "/cart"; });
  });

  // Deal of the Day countdown — counts down to midnight.
  document.querySelectorAll("[data-deal-timer]").forEach((el) => {
    const tick = () => {
      const now = new Date();
      const end = new Date(now);
      end.setHours(23, 59, 59, 999);
      let secs = Math.max(0, Math.floor((end - now) / 1000));
      const h = String(Math.floor(secs / 3600)).padStart(2, "0");
      const m = String(Math.floor((secs % 3600) / 60)).padStart(2, "0");
      const s = String(secs % 60).padStart(2, "0");
      const out = el.querySelector("strong") || el;
      out.textContent = h + ":" + m + ":" + s;
    };
    tick();
    setInterval(tick, 1000);
  });

  // Back to top.
  const backTop = document.getElementById("back-to-top");
  if (backTop) {
    backTop.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
  }

  // ---- "Deliver to" location picker ----------------------------------------
  const deliverBox = document.getElementById("deliver-box");
  if (deliverBox) {
    const DELIVER_KEY = "amazone_deliver_to";
    const DELIVER_LIST_KEY = "amazone_saved_addresses";
    const toggle = document.getElementById("deliver-toggle");
    const pop = document.getElementById("deliver-pop");
    const name = document.getElementById("deliver-name");
    const input = document.getElementById("deliver-input");
    const apply = document.getElementById("deliver-apply");
    const locate = document.getElementById("deliver-locate");
    const mapsKey = deliverBox.dataset.mapsKey || "";
    const openMap = document.getElementById("deliver-open-map");
    let autocomplete = null;
    let map = null;
    let marker = null;
    let mapsLoaded = false;

    // Remember the last chosen location.
    let saved = null;
    try { saved = JSON.parse(localStorage.getItem(DELIVER_KEY)); } catch { saved = null; }
    if (saved && saved.label) name.textContent = saved.label;
    updateOpenMapLink();

    function updateOpenMapLink() {
      if (!openMap) return;
      if (saved && typeof saved.lat === "number" && typeof saved.lng === "number") {
        openMap.href = "https://www.google.com/maps/search/?api=1&query=" + saved.lat + "," + saved.lng;
      } else if (saved && saved.label) {
        openMap.href = "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(saved.label);
      }
    }

    function saveDeliver(loc) {
      localStorage.setItem(DELIVER_KEY, JSON.stringify(loc));
      saved = loc;
      name.textContent = loc.label;
      updateOpenMapLink();

      // Keep the "Manage delivery locations" page's saved list in sync.
      let list = [];
      try { list = JSON.parse(localStorage.getItem(DELIVER_LIST_KEY)); } catch { list = []; }
      if (!Array.isArray(list)) list = [];
      if (!list.some((a) => a.label === loc.label)) {
        list.push({ label: loc.label, lat: loc.lat, lng: loc.lng });
        localStorage.setItem(DELIVER_LIST_KEY, JSON.stringify(list));
      }

      closePop();
    }

    function openPop() {
      pop.hidden = false;
      toggle.setAttribute("aria-expanded", "true");
      loadMaps();
      setTimeout(() => input && input.focus(), 0);
    }

    function closePop() {
      pop.hidden = true;
      toggle.setAttribute("aria-expanded", "false");
    }

    toggle.addEventListener("click", (e) => {
      e.stopPropagation();
      pop.hidden ? openPop() : closePop();
    });
    document.addEventListener("click", (e) => {
      if (!deliverBox.contains(e.target)) closePop();
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closePop();
    });

    // Lazy-load the Google Maps script only when the popover is first opened.
    function loadMaps() {
      if (!mapsKey || mapsLoaded || window.google) return;
      mapsLoaded = true;
      const script = document.createElement("script");
      script.src = "https://maps.googleapis.com/maps/api/js?key=" + encodeURIComponent(mapsKey) + "&libraries=places,marker&loading=async";
      script.async = true;
      script.onload = initMaps;
      document.head.appendChild(script);
    }

    function initMaps() {
      if (!window.google || !window.google.maps) return;
      initAutocomplete();
      initMap();
    }

    function initAutocomplete() {
      if (!window.google || !window.google.maps || !input) return;
      autocomplete = new google.maps.places.Autocomplete(input, { types: ["geocode"] });
      autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;
        const loc = place.geometry.location;
        saveDeliver({
          label: place.formatted_address || input.value,
          lat: loc.lat(),
          lng: loc.lng()
        });
      });
    }

    // Reverse-geocode coordinates to an address (used by the map + locate).
    async function reverseGeocode(lat, lng) {
      const res = await fetch("https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + lng + "&key=" + encodeURIComponent(mapsKey));
      const data = await res.json();
      if (data.results && data.results[0]) return data.results[0].formatted_address;
      return lat.toFixed(4) + ", " + lng.toFixed(4);
    }

    // Interactive map: click or drag the pin to choose a delivery spot.
    function initMap() {
      const mapEl = document.getElementById("deliver-map");
      if (!mapEl || !window.google.maps.Map) return;
      mapEl.hidden = false;

      const center = (saved && typeof saved.lat === "number")
        ? { lat: saved.lat, lng: saved.lng }
        : { lat: 47.6062, lng: -122.3321 }; // Amazon HQ — the default "Your Home"

      map = new google.maps.Map(mapEl, {
        center,
        zoom: 12,
        mapTypeControl: false,
        fullscreenControl: false
      });
      marker = new google.maps.Marker({ map, position: center, draggable: true });

      async function pick(latLng) {
        const lat = latLng.lat();
        const lng = latLng.lng();
        marker.setPosition(latLng);
        let label;
        try { label = await reverseGeocode(lat, lng); } catch { label = lat.toFixed(4) + ", " + lng.toFixed(4); }
        saveDeliver({ label, lat, lng });
      }

      google.maps.event.addListener(map, "click", (e) => pick(e.latLng));
      google.maps.event.addListener(marker, "dragend", () => pick(marker.getPosition()));
    }

    function applyTyped() {
      const label = (input.value || "").trim();
      if (label) saveDeliver({ label });
    }
    apply.addEventListener("click", applyTyped);
    input.addEventListener("keydown", (e) => { if (e.key === "Enter") { e.preventDefault(); applyTyped(); } });

    // Quick city chips.
    deliverBox.querySelectorAll("[data-place]").forEach((chip) => {
      chip.addEventListener("click", () => saveDeliver({ label: chip.dataset.place }));
    });

    // "Use my current location" — browser geolocation + Google reverse geocode.
    locate.addEventListener("click", () => {
      if (!navigator.geolocation) {
        input.placeholder = "Geolocation isn't supported here";
        return;
      }
      locate.disabled = true;
      locate.textContent = "Locating…";
      navigator.geolocation.getCurrentPosition(async (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        let label = "Current location";
        if (mapsKey) {
          try { label = await reverseGeocode(lat, lng); } catch { /* keep generic label */ }
        }
        if (marker) marker.setPosition({ lat, lng });
        if (map) map.setCenter({ lat, lng });
        saveDeliver({ label, lat, lng });
      }, () => {
        input.placeholder = "Location permission denied";
      }).then(() => {
        locate.disabled = false;
        locate.textContent = "📌 Use my current location";
      }).catch(() => {
        locate.disabled = false;
        locate.textContent = "📌 Use my current location";
      });
    });
  }
});
