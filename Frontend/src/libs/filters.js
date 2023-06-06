import Vue from 'vue'

Vue.filter('formatMoney', function(value, decimal_place) {
    if(!decimal_place) {
        return value
    }
    const amount = Number(value)
        .toFixed(decimal_place)
        .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");

    return amount
});

Vue.filter('trim', (value, max) => {
    if(!value) {
        return value
    }
    const len = value.length
    return len > max
        ? `${value.substring(0, max)}...`
        : value
});
