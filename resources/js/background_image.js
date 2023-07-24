class Background_image {
    static getRandomImage(){
        const background_path = '../layout/images/pages/login/'
        const background_images = []
        background_images[0] = 'dose-media-w1920.jpg'
        background_images[1] = 'george-bakos-w1920.jpg'
        background_images[2] = 'leone-venter-w1920.jpg'
        background_images[3] = 'matthew-henry-w1920.jpg'
        background_images[4] = 'remy_loz-w1920.jpg'
        const rand_img = Math.floor(Math.random() * 5)
        return background_path + background_images[rand_img]
    }
}
export {Background_image}
