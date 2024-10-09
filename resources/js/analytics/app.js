
const streamAnalytics = {
    event: {
        name: null,
        data: {}
    },
    request: function (event, data) {
        return fetch(`//${document.streamAnalyticsApiUrl}/api/analytics/${event}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify({
                data,
                key: document.streamAnalyticsKey
            })
        }).then(response => response.json()).catch(err => err)
    },
    track: function (event, data) {
        this.request(event, data).then(data => {
            console.log(data)
        })
    }
}

window.streamAnalytics = streamAnalytics

export default streamAnalytics
