import {createRouter, createWebHashHistory} from 'vue-router'

//NOTA: ESTA APP NÃO USA O VUE ROUTER.
//ESTÁ A SER MANTIDO POR CAUSA DE ALGUNS COMPONENTES PRIMEVUE.

const routes = []

const router = createRouter({
    history: createWebHashHistory(),
    routes,
    scrollBehavior () {
        return { left: 0, top: 0 }
    }
})

export default router
