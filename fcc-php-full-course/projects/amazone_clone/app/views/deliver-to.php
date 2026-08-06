<?php
// views/deliver-to.php — full-size interactive map + saved delivery addresses.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Manage your delivery locations</h1>
<p class="amz-page-sub">Choose where you want your orders delivered. Locations are saved on this device only.</p>

<div class="amz-map-page" data-maps-key="<?php echo e($mapsKey); ?>">
  <section class="amz-map-card">
    <h2>Find a location</h2>
    <?php if (!$hasMapsKey) { ?>
      <div class="amz-alert amz-alert-info">
        Add a <code>google_maps.api_key</code> to <code>app/config.php</code> to enable the interactive map and address
        search. You can still type an address or ZIP code below.
      </div>
    <?php } ?>
    <div class="amz-map-search">
      <input id="map-search-input" type="text" placeholder="Enter a city, ZIP code or address" autocomplete="off">
      <button class="amz-btn amz-btn-yellow" id="map-locate" type="button">&#128205; Use my location</button>
    </div>
    <div id="map-canvas" class="amz-map-canvas"<?php echo $hasMapsKey ? "" : " data-disabled"; ?>>
      <?php if (!$hasMapsKey) { ?>
        <div class="amz-map-empty">Interactive map unavailable &mdash; no Google Maps API key.</div>
      <?php } ?>
    </div>
    <div class="amz-map-readout">
      <span id="map-readout-label">Search above or choose a spot on the map.</span>
      <button class="amz-btn amz-btn-yellow" id="map-deliver-here" type="button" disabled>Deliver to this location</button>
    </div>
  </section>

  <aside class="amz-map-list">
    <h2>Saved addresses</h2>
    <p class="amz-map-list-empty" id="saved-empty">No saved addresses yet &mdash; pick a location and it'll show up here.</p>
    <ul id="saved-addresses" class="amz-address-list"></ul>
  </aside>
</div>

<?php include __DIR__ . "/layout/footer.php"; ?>
<script src="/js/deliver.js"></script>
