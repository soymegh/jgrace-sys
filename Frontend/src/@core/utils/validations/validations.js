import {configure, extend, localize} from 'vee-validate'
import {
    alpha as rule_alpha,
    alpha_dash as rule_alpha_dash,
    alpha_num as rule_alpha_num,
    between as rule_between,
    confirmed as rule_confirmed,
    digits as rule_digits,
    email as rule_email,
    integer as rule_integer,
    length as rule_length,
    min as rule_min,
    regex as rule_regex,
    required as rule_required,
} from 'vee-validate/dist/rules'
import es from 'vee-validate/dist/locale/es.json'
// eslint-disable-next-line object-curly-newline
import {validatorCreditCard, validatorPassword, validatorPositive, validatorUrlValidator} from './validators'

configure({
    generateMessage: (context) => {
        const messages = {
            rule_required: `El campo ${context.field} es requerido.`,
            min: `This field ${context.field} is too short.`,
            max: `This field ${context.field} is too long.`,
            alpha_spaces: `This field ${context.field} can only contain letters and spaces.`,
            email: `This field ${context.field} is not a valid email.`,
            min_value: `This field ${context.field} is too low.`,
            max_value: `This field ${context.field} is too high.`,
            excluded: "This field is not allowed.",
            country_excluded: "We do not allow users from this location",
            password_mismatch: `This field ${context.field} does not match.`,
        };
        return messages[context.rule.name]
            ? messages[context.rule.name]
            : `El campo ${context.field} no es válido`;
    },
});

// ////////////////////////////////////////////////////////
// General
// ////////////////////////////////////////////////////////

export const required = extend('required', rule_required)

export const email = extend('email', rule_email)

export const min = extend('min', rule_min)

export const confirmed = extend('confirmed', rule_confirmed)

export const regex = extend('regex', rule_regex)

export const between = extend('between', rule_between)

export const alpha = extend('alpha', rule_alpha)

export const integer = extend('integer', rule_integer)

export const digits = extend('digits', rule_digits)

export const alphaDash = extend('alpha-dash', rule_alpha_dash)

export const alphaNum = extend('alpha-num', rule_alpha_num)

export const length = extend('length', rule_length)

export const positive = extend('positive', {
    validate: validatorPositive,
    message: 'Please enter positive number!',
})

export const credit = extend('credit-card', {
    validate: validatorCreditCard,
    message: 'It is not valid credit card!',
})

export const password = extend('password', {
    validate: validatorPassword,
    message: 'Tu {_field_} deberá contener al menos una letra mayuscula, una minuscula, un caracter especial y un número',
})

export const url = extend('url', {
    validate: validatorUrlValidator,
    message: 'URL is invalid',
})

// Install English and Arabic localizations.
localize({
    /*  en: {
        messages: en.messages,
        names: {
          email: 'Email',
          password: 'Password',
        },
        fields: {
          password: {
            min: '{_field_} is too short, you want to get hacked?',
          },
        },
      },*/
    es: {
        messages: es.messages,
        names: {
            email: 'Correo electronico',
            password: 'Contraseña',
        },
        fields: {
            password: {
                min: '{_field_} es demasiado corto.',
            },
        },
    },
});
// ////////////////////////////////////////////////////////
// NOTE:
// Quasar validation for reference only
// Remove this note once development is finished and make sure to
// to convert all of them in veevalidate version
// ////////////////////////////////////////////////////////

// export const required = (val) => {
//   return (val && val.length > 0) || '*Field is required'
// }

// export const required_obj = (obj) => {
//   if (obj === null || obj === undefined) return '*Field is required'
//   return (Object.entries(obj).length > 0 && obj.constructor === Object) || '*Field is required'
// }

// export const no_blank_spaces_arr = (arr) => {
//   return arr.every(val => (val.trim() && val.trim().length > 0)) || 'Blank Spaces are not allowed'
// }

// export const url = val => {
//   // If blank return
//   if (val === undefined || val === null || val.length === 0) return true

//   // Used
//   // https://stackoverflow.com/questions/4314741/url-regex-validation

//   // Other
//   // https://stackoverflow.com/questions/5717093/check-if-a-javascript-string-is-a-url
//   // https://www.w3resource.com/javascript-exercises/javascript-regexp-exercise-9.php
//   // https://www.geeksforgeeks.org/how-to-validate-url-using-regular-expression-in-javascript/

//   /* eslint-disable no-useless-escape */
//   const re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/
//   /* eslint-enable no-useless-escape */
//   return re.test(val) || 'URL is invalid'
// }

// export const date = val => {

//   // If blank return
//   if (val === undefined || val === null || val.length === 0) return true

//   // https://github.com/quasarframework/quasar/blob/dev/ui/src/utils/patterns.js
//   return /^-?[\d]+\/[0-1]\d\/[0-3]\d$/.test(val) || 'Date is invalid'
// }

// export const max = (val, max) => {

//   // If blank return
//   if (val === undefined || val === null) return true

//   return val.length <= max || `More than ${max} characters are not allowed`
// }

// export const max_arr = (val, max) => {
//   return val.length <= max || `More than ${max} values are not allowed`
// }

// export const min = (val, min) => {

//   // If blank return
//   if (val === undefined || val === null || val.length === 0) return true

//   return val.length >= min || `Minimum ${min} characters are required`
// }

// export const num_range = (val, min, max) => {

//   // If blank return
//   if (val === undefined || val === null || val.length === 0) return true

//   const msg = 'Value is invalid'
//   if (min === null) return val <= max || msg
//   else if (max === null) return val >= min || msg
//   else return (val >= min && val <= max) || msg
// }