<template>
    <div v-if="user && system">
        <h1 class=" alignC mb20">{{$t('p.p.title', {name: user.name + (user.email ? ' (' + user.email + ')' : '')})}}</h1>
        <div id="profileBlock">
            <div id="profileMenuBlock">
                <el-menu :default-active="$route.path" active-text-color="#FF7100" router>
                    <el-menu-item index="/profile/info">
                        <span>{{$t('p.p.personal')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/profile/plan">
                        <span>{{$t('p.p.plan')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/profile/lessons">
                        <span>{{$t('p.p.yourLessons')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/profile/history">
                        <span>{{$t('p.p.history')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/profile/settings">
                        <span>{{$t('p.p.settings')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/profile/statistic" disabled>
                        <span>{{$t('p.p.statistic')}}</span>
                    </el-menu-item>
                </el-menu>
            </div>
            <div id="profileViewBlock">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped>
    #profileBlock {
        height:100%;
        display:flex;
        padding: 5px;
        #profileMenuBlock {
            width:300px;
        }
        #profileViewBlock {
            width: 100%;
            min-width: 500px;
            padding: 5px;
        }
    }
    .is-active {
        background-color: #f3f3f3 !important;
    }
</style>
<script>
export default {
    computed: {
        ...mapState(['user', 'system'])
    }
}
</script>