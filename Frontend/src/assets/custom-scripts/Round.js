export default {
    round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
},

    epsilon(value, decimals){
        decimals = decimals || 0;
        const p = Math.pow(10, decimals);
        const n = (value * p) * (1 + Number.EPSILON);
        return Math.round(n) / p;
    },
    /**
     * Ajuste decimal de un número.
     *
     * @param	{String}	type	El tipo de ajuste.
     * @param	{Number}	value	El número.
     * @param	{Integer}	exp		El exponente(el logaritmo en base 10 del ajuste).
     * @returns	{Number}			El valor ajustado.
     */
    decimalAdjust(type, value, exp) {
    // Si el exp es indefinido o cero...
    if (typeof exp === 'undefined' || +exp === 0) {
        return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un número o el exp no es un entero...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
        return NaN;
    }
    // Cambio
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Volver a cambiar
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
}
}
