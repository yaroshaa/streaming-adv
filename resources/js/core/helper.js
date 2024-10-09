/**
 * generate random number
 *
 * @param max
 * @returns {number}
 */
export function randomInt(max) {
    return Math.floor(Math.random() * max);
}

/**
 * generate random float number
 *
 * @param max
 * @returns {number}
 */
export function randomFloat(max) {
    return randomInt(max) + Math.random();
}

/**
 * get random array item
 *
 * @param array
 * @returns {*}
 */
export function randomFromArray(array) {
    return array[Math.floor(Math.random() * array.length)];
}

/**
 * generate random coordinate
 *
 * @param center
 * @param radius
 * @returns {{lat: *, long: *}}
 */
export function generateRandomPoint(center, radius) {
    const x0 = center.long;
    const y0 = center.lat;
    // Convert Radius from meters to degrees.
    const rd = radius / 111300;

    const u = Math.random();
    const v = Math.random();

    const w = rd * Math.sqrt(u);
    const t = 2 * Math.PI * v;
    const x = w * Math.cos(t);
    const y = w * Math.sin(t);

    const xp = x / Math.cos(y0);

    return {lat: y + y0, long: xp + x0};
}

/**
 * generate array of random points
 *
 * @param center
 * @param radius
 * @param count
 * @returns {[]}
 */
export function generateRandomPoints(center, radius, count) {
    const points = [];
    for (let i = 0; i < count; i++) {
        points[i] = generateRandomPoint(center, radius);
    }
    return points;
}

/**
 * get address from google map by coordinates
 *
 * @param geocoder
 * @param point
 * @returns {Promise<unknown>}
 */
export async function geocodeLatLng(geocoder, point) {
    const latlng = {
        lat: parseFloat(point.lat),
        lng: parseFloat(point.long),
    };
    return new Promise((resolve, reject) => {
        geocoder.geocode({location: latlng}, (results, status) => {
            if (status === 'OK') {
                if (results[0]) {
                    resolve(results[0].formatted_address);
                } else {
                    reject(new Error('No results found'));
                }
            } else {
                reject(new Error('Geocoder failed due to: ' + status));
            }
        });
    });
}

/**
 * How much $ for a 🦄? -> How much \\$ for a 🦄\\?
 * @param string
 * @returns {string}
 */
export function escapeRegExp(string) {
    return string.replace(/[|\\{}()[\]^$+*?.]/g, '\\$&').replace(/-/g, '\\x2d');
}

/**
 * Highlight searched text
 * @param {string} text
 * @param {object} search
 * @returns {string}
 */
export function highlight(text, search) {
    let words = Object.keys(search);
    if (words.length === 0) {
        return text;
    }

    let re = new RegExp(words.map((word) => escapeRegExp(word)).join('|'), 'gi');
    return text.replace(re, function (matched) {
        return `<span class="highlight ${search[matched.toLocaleLowerCase()]}">${matched}</span>`;
    });
}

/**
 * All request errors from laravel to string
 *
 * @param e
 * @returns {string}
 */
export function errorResponseToString(e) {
    let errorString = '';
    // eslint-disable-next-line
    for (const [name, errors] of Object.entries(e.response.data.errors)) {
        errorString += errors.join('\n');
    }

    return errorString;
}
