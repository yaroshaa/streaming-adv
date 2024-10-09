/* prettier-ignore */
/* eslint-disable */

const EventBus = function () {
    let events = []
    let locked = []
    let queue = []

    return {
        unlock(event, lockKey) {
            if (queue.hasOwnProperty(event) && queue[event].hasOwnProperty(lockKey) && null !== queue[event][lockKey]) {
                queue[event][lockKey].handler(queue[event][lockKey].payload)
                queue[event][lockKey] = null

                return
            }
            locked[event][lockKey] = false
        },

        on(event, handler, lockKey) {
            if (!events.hasOwnProperty(event)) {
                events[event] = [];
            }

            events[event].push({handler, lockKey})

            if (!locked.hasOwnProperty(event)) {
                locked[event] = [];
            }

            locked[event][lockKey] = false
        },

        emit(event, payload) {
            if (!events.hasOwnProperty(event)) {
                return
            }

            events[event].forEach(({handler, lockKey}) => {
                if (locked[event][lockKey]) {
                    if (!queue.hasOwnProperty(event)) {
                        queue[event] = []
                    }

                    queue[event][lockKey] = {handler, payload};

                    return
                }

                locked[event][lockKey] = true

                handler(payload)
            })
        },
        off(event) {
            if (!events.hasOwnProperty(event)) {
                return;
            }

            delete events[event];
        }
    }
}

export default EventBus();
