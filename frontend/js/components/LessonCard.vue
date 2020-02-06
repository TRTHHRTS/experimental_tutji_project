<template>
    <el-card class="card" :body-style="{ padding: '0px' }" :style="'width: ' + width + 'px'">
        <div style="position: relative;">
            <div class="upper_block">
                <div class="categories" style="background: rgba(255, 255, 255, 0.655);">
                    <span class="xs-text p5" v-for="category in lesson.categories" :key="category.id">{{ category.name }}</span>
                </div>
                <el-rate v-if="rating.COUNT > 0" v-model="rating.AVG_ACCURATE" disabled></el-rate>
            </div>
            <img v-if="showImage && lesson.images.length > 0" :src="lesson.images[0].url" class="image"
                 alt="картинка урока" @click="$router.push('/lesson/'+lesson.id)">
            <img v-if="showImage && lesson.images.length <= 0" src="../../images/default_lesson_image.jpg" class="image"
                 alt="картинка урока" @click="$router.push('/lesson/'+lesson.id)">
            <div @click="emitOpeningProfile(lesson.user_id)" class="avatar">
                <img v-if="lesson.user.user_details.photo_url" :src="lesson.user.user_details.photo_url" style="height: 100%; border-radius: 50%;"/>
                <img v-else src="../../images/avatar.png" style="height: 100%; border-radius: 50%; box-shadow: 0 0 10px rgba(0,0,0,0.5);"/>
            </div>
        </div>
        <div class="clignC"><span @click="emitOpeningProfile(lesson.user_id)" class="internal_link" style="overflow: hidden;">{{lesson.user.name}}</span></div>
        <div class="bottom clearfix alignC">
            <div class="mb10"><router-link :to="'/lesson/' + lesson.id"><strong>{{ lesson.name }} ({{lesson.aging.name}})</strong></router-link></div>
            <template v-if="needTimes">
                <el-tag v-for="t in lesson.reserved_times" :key="t.id" size="mini" type="info" class="m2">
                    {{t.lesson_date | formatDate}}{{$t('p.comps.card.inSuffix')}}{{t.lesson_time}}
                </el-tag>
            </template>
            <div class="alignR"><strong>{{lesson.price + " ₽"}}</strong></div>
        </div>
    </el-card>
</template>
<style scoped>
.image {
    max-width: 100%;
    max-height: 100%;
    display: block;
    cursor: pointer;
}
.card {
    vertical-align: top;
    display: inline-block;
    margin: 10px;
}
.upper_block {
    position: absolute;
    width: 100%;
    top: 0;
}
.categories {
    padding: 0 5px 0 5px;
}
.avatar {
    width: 50px;
    height: 50px;
    display: inline-block;
    cursor: pointer;
    position: absolute;
    bottom: 0;
    left: 0;
    margin: 0 0 5px 5px;
}
.bottom {
    padding: 0 5px 0 5px;
    margin: 5px 0 5px 0;
}
</style>
<script>
export default{
    props: {
        showImage: Boolean,
        lesson: Object,
        width: String,
        needTimes: {type: Boolean, default: false}
    },
    data() {
        return {
            RES_PUPILS_COUNT: null,
            RES_TIMES_COUNT: null,

            rating: {
                COUNT: 0,
                AVG: 0,
                AVG_ACCURATE: 0,
            },

            moment: moment,
            lodash: _,
        }
    },
    methods: {
        emitOpeningProfile(userId) {
            this.$emit('openProfile', Number(userId));
        },
        getTruncatedString(str) {
            return _.truncate(str, {'length': 150})
        }
    },
    mounted() {
        if (this.lesson.reserved_lessons) {
            this.RES_PUPILS_COUNT = _.sum(_.map(this.lesson.reserved_lessons, 'count'));
        }
        let reviews = this.lesson.reviews;
        if (reviews) {
            let ratingArray = _.map(reviews, 'rating');
            this.rating.COUNT = _.size(ratingArray);
            let ratingDivided = _.divide(_.sum(ratingArray), this.rating.COUNT);
            this.rating.AVG = _.round(ratingDivided);
            this.rating.AVG_ACCURATE = _.round(ratingDivided, 3)
        }
    }
}
</script>
