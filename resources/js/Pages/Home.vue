<script setup>
import { ref, onMounted, toRefs } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

import LikesSection from '@/Components/LikesSection.vue'
import ShowPostOverlay from '@/Components/ShowPostOverlay.vue'

import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide, Navigation } from 'vue3-carousel'

import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'

let wWidth = ref(window.innerWidth)
let currentSlide = ref(0)
let currentPost = ref(null)
let openOverlay = ref(false)

const props = defineProps({ posts: Object, allUsers: Object })
const { posts, allUsers } = toRefs(props)

onMounted(() => {
    window.addEventListener('resize', () => {
        wWidth.value = window.innerWidth
    })
})

const addComment = (object) => {
    router.post('/comments', {
        post_id: object.post.id,
        user_id: object.user.id,
        comment: object.comment
    }, {
        onFinish: () => updatedPost(object),
    }
    )
}

const deleteFunc = (object) => {
    let url = ''
    if (object.deleteType === 'Post') {
        url = '/posts/' + object.id
    } else {
        url = '/comments/' + object.id
    }

    router.delete(url, {
        onFinish: () => updatedPost(object),
    })

    if (object.deleteType === 'Post') {
        openOverlay.value = false
    }
}

const updateLike = (object) => {
    let deleteLike = false
    let id = null

    for (let i = 0; i < object.post.likes.length; i++) {
        const like = object.post.likes[i];
        if (like.user_id === object.user.id && like.post_id === object.post.id) {
            deleteLike = true
            id = like.id
        }
    }

    if (deleteLike) {
        router.delete('/likes/' + id, {
            onFinish: () => updatedPost(object),
        })
    } else {
        router.post('/likes', {
            post_id: object.post.id,
        },{
            onFinish: () => updatedPost(object),
        })
    }
}

const updatedPost = (object) => {
    for (let i = 0; i < posts.value.data.length; i++) {
        const post = posts.value.data[i];
        if (post.id === object.post.id) {
            currentPost.value = post
        }
    }
}
</script>

<template>
    <Head title="panitsgram" />

    <MainLayout>
        <div class="mx-auto lg:pl-0 md:pl-[80px] pl-0">
            <Carousel
                v-model="currentSlide"
                class="max-w-[700px] mx-auto"
                :items-to-show="wWidth >= 768 ? 8 : 6"
                :items-to-scroll="4"
                :wrap-around="true"
                :transition="500"
                snapAlign="start"
            >
                <!-- v-for="slide in allUsers" -->
                <Slide v-for="slide in 10" :key="slide" class="h-60">
                    <!-- :href="route('users.show', { id: slide.id })" -->
                    <Link href="/" class="relative mx-auto text-center mt-4 px-2 cursor-pointer">
                        <div class="absolute z-[-1] -top-[5px] left-[4px] rounded-full rotate-45 w-[64px] h-[64px] contrast-[1.3]  bg-gradient-to-t from-yellow-300 to-purple-500 via-red-500">
                            <div class="rounded-full ml-[3px] mt-[3px] w-[58px] h-[58px] bg-white" />
                        </div>
                        <!-- :src="slide.file" -->
                        <img class="rounded-full w-[56px] h-[56px] -mt-[1px]" src="https://picsum.photos/id/54/200/300">
                        <!-- {{ slide.name }} -->
                        <div class="text-xs mt-2 w-[60px] truncate text-ellipsis overflow-hidden">Slide name</div>
                    </Link>
                </Slide>

                <template #addons>
                    <Navigation />
                </template>
            </Carousel>
            <!-- v-for="post in posts.data" -->
            <div id="Posts" class="px-4 max-w-[600px] mx-auto mt-10" v-for="post in 10" :key="post">
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center">
                        <!--  :href="route('users.show', { id: post.user.id })" -->
                        <Link href="/" class="flex items-center">
                            <!--  :src="post.user.file" -->
                            <img class="rounded-full w-[38px] h-[38px]" src="https://picsum.photos/id/54/200/300">
                            <!--  {{ post.user.name }} -->
                            <div class="ml-4 font-extrabold text-[15px]">POST USER NAME</div>
                        </Link>
                        <div class="flex items-center text-[15px] text-gray-500">
                            <span class="-mt-5 ml-2 mr-[5px] text-[35px]">.</span>
                            <!-- {{ post.created_at }}  -->
                            <div>POST CREATED AT 16/08/2024</div>
                        </div>
                    </div>

                    <DotsHorizontal class="cursor-pointer" :size="27"/>
                </div>

                <div class="bg-black rounded-lg w-full min-h-[400px] flex items-center">
                    <!-- :src="post.file" -->
                    <img class="mx-auto w-full" src="https://picsum.photos/id/54/200/300" />
                </div>

                <LikesSection
                    :post="post"
                    @like="$event => updateLike($event)"
                />
                <!-- {{ post.likes.length }} -->
                <div class="text-black font-extrabold py-1">1000 likes</div>
                <div>
                    <!-- {{ post.user.name }} -->
                    <span class="text-black font-extrabold">POST USER NAME</span>
                    <!-- {{ post.text }} -->
                      POST TEXT
                </div>
                <!-- @click="$event => currentPost = post,  openOverlay = true" -->
                <button
                    @click="$event => openOverlay = true"
                    class="text-gray-500 font-extrabold py-1"
                >
                    <!-- {{ post.comments.length }} -->
                    View all 1000 comments
                </button>
            </div>

            <div class="pb-20"></div>
        </div>
    </MainLayout>
    <!-- @addComment="addComment($event)"
        @updateLike="updateLike($event)"
        @deleteSelected="deleteFunc($event);" -->

    <ShowPostOverlay
        v-if="openOverlay"
        :post="currentPost"
        @closeOverlay="$event => openOverlay = false"
    />
</template>

<style>
    .carousel__prev,
    .carousel__next,
    .carousel__prev:hover,
    .carousel__next:hover {
        color: rgb(49, 49, 49);
        background-color: rgb(255, 255, 255);
        border-radius: 100%;
        margin: 0 20px;
    }
</style>
