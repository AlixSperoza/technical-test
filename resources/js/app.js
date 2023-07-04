import { createApp } from 'vue'

import Layout from './components/Layout.vue'

import routes from './routes/app'

import { EventEmitter } from 'events'
import axios from 'axios'

EventEmitter.defaultMaxListeners = 1000

window.EventBus = new EventEmitter()

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content

var app = createApp({})

app.config.devtools = false
app.config.performance = true

app.use(routes)

app.component('layout', Layout)

app.mount('#app')

window.Vue = app
