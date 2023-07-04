import { createRouter, createWebHistory } from 'vue-router'

import dashboard from './dashboard'

export default createRouter({
    history: createWebHistory(),
    routes: [
        ...dashboard,
    ]
})
