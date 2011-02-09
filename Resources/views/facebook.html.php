<div id="fb-root"></div>

<script type="text/javascript">
window.fbAsyncInit = function () {
    FB.init(<?php echo json_encode(array(
        'appId'   => $appId,
        'cookie'  => $cookie,
        'xfbml'   => $xfbml,
        'session' => $session,
        'status'  => $status,
        'logging' => $logging,
    )) ?>);
    <?php $view['slots']->output('fbAsyncInit') ?>
};

(function () {
    var a = document.createElement("script");
    a.src = document.location.protocol + "//connect.facebook.net/<?php echo $culture ?>/all.js";
    a.async = true;
    document.getElementById("fb-root").appendChild(a)
})();
</script>
