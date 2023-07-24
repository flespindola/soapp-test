export default {

    /**
     * Verifica se o array/objecto errors tem um erro começado em...
     *
     * @param {Array} errors
     * @param {Array|String} name
     * @returns {boolean}
     */
    hasErrors(errors = [], name){
        let has_errors = false
        if(errors === null || errors === undefined){
            return has_errors
        }

        let names = []
        if(Array.isArray(name)){
            names = name
        }else{
            names = [name]
        }

        if(names.length === 0){
            return has_errors
        }

        let errors_array = Object.getOwnPropertyNames(errors)
        if(errors_array.length === 0){
            return has_errors
        }

        Object.keys(errors_array).forEach(key => {
            let error = errors_array[key]
            Object.keys(names).forEach(k => {
                let the_name = names[k]
                if(error.startsWith(the_name)){
                    has_errors = true
                }
            })
        })

        return has_errors
    },

    validaContribuinte(value) {
        if(value == null || value == undefined){
            return false
        }
        const nif = typeof value === 'string' ? value : value.toString()
        const validationSets = {
            one: ['1', '2', '3', '5', '6', '8'],
            two: ['45', '70', '71', '72', '74', '75', '77', '79', '90', '91', '98', '99']
        }
        if (nif.length !== 9) return false
        if (!validationSets.one.includes(nif.substr(0, 1)) && !validationSets.two.includes(nif.substr(0, 2))) return false
        const total = nif[0] * 9 + nif[1] * 8 + nif[2] * 7 + nif[3] * 6 + nif[4] * 5 + nif[5] * 4 + nif[6] * 3 + nif[7] * 2
        const modulo11 = (Number(total) % 11)
        const checkDigit = modulo11 < 2 ? 0 : 11 - modulo11
        return checkDigit === Number(nif[8])
    }
}
