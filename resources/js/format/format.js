let currentCurrency = 'EUR';
let currencyFormatter = new Intl.NumberFormat('en', {
    style: 'currency',
    currency: currentCurrency,
    notation: 'compact',
});

let numberFormatter = new Intl.NumberFormat('en');

let percentFormatter = new Intl.NumberFormat('en', {
    style: 'percent',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value, currency = currentCurrency) => {
    if (currentCurrency !== currency) {
        currencyFormatter = new Intl.NumberFormat('en', {
            style: 'currency',
            currency: currency,
            notation: 'compact',
        });
        currentCurrency = currency;
    }
    return currencyFormatter.format(value);
};
const formatNumber = (value) => numberFormatter.format(value);
const formatPercent = (value) => percentFormatter.format(value);

export {formatCurrency, formatNumber, formatPercent};
