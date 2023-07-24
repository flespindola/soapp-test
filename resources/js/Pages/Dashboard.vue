<!-- eslint-disable vue/multi-word-component-names -->

<template>

    <div class="grid">
        <div class="col-12">
            <div class="card">
                <h5>Dashboard</h5>
                <p>This is your empty page template to start building beautiful applications.</p>
            </div>
        </div>
    </div>

    <hr>

    <div class="grid">
        <div class="col-12 md:col-6 lg:col-4">
            <div class="card">
                <h5>{{ words }}</h5>
                <p>This is your empty page template to start building beautiful applications.</p>
            </div>
        </div>
        <div class="col-12 md:col-6 lg:col-4">
            <div class="card">
                <h5>Dashboard</h5>
                <p>This is your empty page template to start building beautiful applications.</p>
            </div>
        </div>
        <div class="col-12 md:col-6 lg:col-4">
            <div class="card">
                <h5>Dashboard</h5>
                <p>This is your empty page template to start building beautiful applications.</p>
            </div>
        </div>
    </div>


</template>


<script setup>
import { ref } from 'vue'
// import { I18n } from 'laravel-vue-i18n'

// const translate = I18n.getSharedInstance()
// console.log(translate)

const words = ref('Words')

</script>
