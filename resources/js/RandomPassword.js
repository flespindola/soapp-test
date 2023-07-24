export default class RandomPassword{
    constructor() {
        /**
         * sets of charachters
         */
        this.upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        this.lower = 'abcdefghijklmnopqrstuvwxyz'
        this.digit = '0123456789'
        this.symbol = '!#-$%&@=?'
        this.all = this.upper + this.lower + this.digit
    }

    /**
     * generate random integer not greater than `max`
     */
    rand (max) {
        return Math.floor(Math.random() * max)
    }

    /**
     * generate random character of the given `set`
     */
    random (set) {
        return set[this.rand(set.length - 1)]
    }

    /**
     * generate an array with the given `length`
     * of characters of the given `set`
     */
    generate (length, set) {
        let result = []
        while (length--) result.push(this.random(set))
        return result
    }

    /**
     * shuffle an array randomly
     */
    shuffle (arr) {
        let result = []
        while (arr.length) {
            result = result.concat(arr.splice(this.rand[arr.length - 1]))
        }

        return result
    }

    /**
     * do the job
     */
    password (length) {
        let result = [] // we need to ensure we have some characters
        result = result.concat(this.generate(1, this.upper)) // 1 upper case
        result = result.concat(this.generate(1, this.lower)) // 1 lower case
        result = result.concat(this.generate(1, this.digit)) // 1 digit
        result = result.concat(this.generate(1, this.symbol)) // 1 symbol
        result = result.concat(this.generate(length - 3, this.all)) // remaining - whatever

        return this.shuffle(result).join('') // shuffle and make a string
    }
}
