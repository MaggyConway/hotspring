<div class="notification-bar">
<?php
if( isset($GLOBALS["notifications"]) && is_array($GLOBALS["notifications"]) ) {
  foreach ($GLOBALS["notifications"] as $key => $notification) {
    print '<div class="toast align-items-center text-white bg-primary border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true" style="
    width: 100%;
    padding: 0;
    margin: 0;
    border-radius: 0;
">
  <div class="d-flex">
    <div class="toast-body">
      <h5>' . $notification['text'] . '</h5>
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>';
  }
}
?>
</div>