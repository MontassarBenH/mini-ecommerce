<?php

class Tracking extends BasePlugin
{
    private $gtmId = 'GTM-XXXXXXX'; // TODO

    public function init()
    {
        // <head> – GTM Script + dataLayer
        $this->registerHook('head_tracking', [$this, 'renderHeadSnippet'], 10);

        // Direkt nach <body> – <noscript>-Fallback
        $this->registerHook('body_noscript_tracking', [$this, 'renderBodyNoScript'], 10);

        // Globale trackEvent()-Funktion
        $this->registerHook('tracking_js', [$this, 'renderTrackingJs'], 10);
    }

    /**
     * GTM-Script im <head>
     */
    public function renderHeadSnippet()
    {
        if (empty($this->gtmId)) {
            return '';
        }

        return <<<HTML
<!-- Google Tag Manager -->
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}

// optionale Initial-Events (Pageview)
window.dataLayer.push({
    'event': 'page_view',
    'page_location': window.location.href,
    'page_path': window.location.pathname,
    'page_title': document.title
});

(function(w,d,s,l,i){
    w[l]=w[l]||[];
    w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
    var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),
        dl=l!='dataLayer'?'&l='+l:'';
    j.async=true;
    j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
    f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$this->gtmId}');
</script>
<!-- End Google Tag Manager -->
HTML;
    }

    /**
     * <noscript>-Iframe direkt nach <body>
     */
    public function renderBodyNoScript()
    {
        if (empty($this->gtmId)) {
            return '';
        }

        return <<<HTML
<!-- Google Tag Manager -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={$this->gtmId}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager -->
HTML;
    }

    /**
     * Globale trackEvent()-Funktion für E-Commerce-Events
     */
    public function renderTrackingJs()
    {
        return <<<HTML
<script>
// Globale Tracking-Funktion
window.dataLayer = window.dataLayer || [];

window.trackEvent = function(eventName, params) {
    params = params || {};
    var payload = Object.assign({ event: eventName }, params);
    window.dataLayer.push(payload);
    if (window.console && console.log) {
        console.log('[trackEvent]', payload);
    }
};
</script>
HTML;
    }
}
