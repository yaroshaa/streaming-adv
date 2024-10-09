(function (document, src, instance) {
    window.streamAnalytics = instance
    instance.init = (siteKey, apiUrl) => {
        document.streamAnalyticsKey = siteKey
        document.streamAnalyticsApiUrl = apiUrl
    }
    instance.track = (eventName, data) => {
        instance.event = {
            name: eventName,
            data: data
        }
    }
    const element = document.createElement('script');
    element.type = 'text/javascript';
    element.id = 'stream-analytics'
    element.src = src
    element.async = 1;

    const e = document.getElementsByTagName('script')[0]
    e.parentNode.insertBefore(element, e)
    element.onload = function () {
        window.streamAnalytics.track(instance.event.name, instance.event.data)
    }
})(document, 'http://127.0.0.1:8001/js/stream-analytics.js', window.streamAnalytics || [])
