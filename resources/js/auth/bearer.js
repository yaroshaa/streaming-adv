/*!
 * @websanova/vue-auth v3.3.8
 * https://websanova.com/docs/vue-auth
 * Released under the MIT License.
 */

const bearer = {
    request: function (req, token) {
        this.http.setHeaders.call(this, req, {
            Authorization: 'Bearer ' + token,
        });
    },
    response: function (res) {
        const headers = this.http.getHeaders.call(this, res);
        let token = headers.Authorization || headers.authorization || res.data.access_token;
        if (token) {
            token = token.split(/Bearer:?\s?/i);
            return token[token.length > 1 ? 1 : 0].trim();
        }
    },
};

export default bearer;
