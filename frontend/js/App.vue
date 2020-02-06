<template>
    <el-container class="h100p">
        <el-header>
            <span @click="$router.push('/home')" class="btn_link homeTitle">{{$t('appName')}}</span>
            <div v-if="user" class="p10 floatR">
                <el-button @click="logout" icon="el-icon-upload2" type="danger" size="small" :loading="logoutLoading" plain>{{$t('mainMenu.logout')}}</el-button>
            </div>
            <el-dropdown id="smallMenuButton" class="m10 floatR" trigger="click" @command="path => $router.push(path)">
                <el-button type="default" size="small" plain>{{$t('mainMenu.menu')}}<i class="el-icon-arrow-down el-icon--right"></i></el-button>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item command="/administrator" v-if="user && isOperator">{{$t('mainMenu.admin')}}</el-dropdown-item>
                    <el-dropdown-item command="/profile" v-if="user">{{$t('mainMenu.profile', {email: user.email})}}</el-dropdown-item>
                    <el-dropdown-item command="/filter"><strong class="successText">{{$t('mainMenu.findLessons')}}</strong></el-dropdown-item>
                    <el-dropdown-item command="/help">{{$t('mainMenu.help')}}</el-dropdown-item>
                    <el-dropdown-item command="/messages" v-if="user">{{$t('mainMenu.messages')}}</el-dropdown-item>
                    <el-dropdown-item command="/faq">{{$t('mainMenu.faq')}}</el-dropdown-item>
                    <el-dropdown-item command="/login" v-if="!user">{{$t('mainMenu.login')}}</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
            <el-menu id="horizontalNav" :default-active="$nextTick(() => {return $route.path})" class="header-menu floatR" mode="horizontal" router>
                <el-menu-item :route="{name: 'admin'}" index="/administrator" v-if="user && isOperator">{{$t('mainMenu.admin')}}</el-menu-item>
                <el-menu-item :route="{name: 'filter'}" index="/filter"><strong class="successText">{{$t('mainMenu.findLessons')}}</strong></el-menu-item>
                <el-menu-item :route="{name: 'help'}" index="/help">{{$t('mainMenu.help')}}</el-menu-item>
                <el-menu-item :route="{name: 'faq'}" index="/faq">{{$t('mainMenu.faq')}}</el-menu-item>
                <el-menu-item :route="{name: 'messages'}" index="/messages" v-if="user">{{$t('mainMenu.messages')}}</el-menu-item>
                <el-menu-item :route="{name: 'login'}" index="/login" v-if="!user">{{$t('mainMenu.login')}}</el-menu-item>
                <el-menu-item :route="{name: 'profileInfo'}" index="/profile/info" v-if="user"><strong>{{$t('mainMenu.profile', {email: user.email ? user.email : '...'})}}</strong></el-menu-item>
            </el-menu>
            <div class="newLessonBlock warningText floatR pointer" @click="openNewLessonDialog"><strong>{{$t('mainMenu.newLesson')}}</strong></div>
            <el-dialog :title="$t('creating.title')" :visible.sync="newLessonDialogVisible" width="300px">
                <span>{{$t('creating.text')}}</span>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="newLessonDialogVisible = false">{{$t('p.common.cancelBtn')}}</el-button>
                    <el-button type="success" @click="createNewLesson" :loading="createLoading">{{$t('creating.yes')}}</el-button>
                </span>
            </el-dialog>
        </el-header>

        <el-main>
            <div style="margin-left: 20px; min-width: 900px;">
                <router-view></router-view>
            </div>
        </el-main>

        <el-footer height="40px">
            <table style="width: 100%; height: 100%;">
                <tr>
                    <td valign="middle">© TUTJI, 2019</td>
                    <td valign="middle" class="alignR">
                        <el-dropdown @command="changeLocale">
                            <el-button type="default" size="mini" plain>
                                <i class="icon-globe w12" style="vertical-align: top;"></i> {{$t('mainMenu.lang')}}<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="ru"><i class="icon-flag-ru"></i> Русский</el-dropdown-item>
                                <el-dropdown-item command="en"><i class="icon-flag-en"></i> English</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </td>
                </tr>
            </table>
        </el-footer>
    </el-container>
</template>
<style lang="scss">
.homeTitle {
    font-weight: bold;
    font-size: 150%;
    height: 60px;
    line-height: 60px;
    white-space: nowrap;
    margin-left: 20px;
}
.header-menu {
    background-color: #F2F4F6;
}
.danger-color {
    color: rgb(250, 85, 85);
i {
    color: inherit;
}
}
.el-menu--horizontal {
    border-bottom: none;
}
.el-main {
    padding: 0;
}
.el-header {
    background-color: #F2F4F6;
    padding: 0;
}
.el-footer {
    background-color: #F2F4F6;
}
.box {
    width: 400px;
}
.item {
    margin: 4px;
}
.newLessonBlock {
    height: 60px;
    line-height: 60px;
    white-space: nowrap;
}
</style>
<script>
export default {
    data() {
        return {
            logoutLoading: false,
            createLoading: false,
            newLessonDialogVisible: false
        }
    },
    computed: {
        ...mapState(['user']),
        ...mapGetters(['isOperator'])
    },
    methods: {
        async changeLocale(newLocale) {
            this.$cookie.set('lang', newLocale, 10080);
            this.$router.go(0);
        },
        openNewLessonDialog() {
            if (this.user) {
                this.newLessonDialogVisible = true;
            } else {
                this.$router.push("/register");
            }
        },
        async createNewLesson() {
            try {
                this.createLoading = true;
                const newLessonId = await this.post("/create");
                this.$router.push("/edit/" + newLessonId);
            } finally {
                this.createLoading = false;
                this.newLessonDialogVisible = false;
            }
        },
        async logout(event) {
            try {
                this.logoutLoading = true;
                await this.getWithErrors('/logout');
                this.$store.commit('logoutUser', null);
                this.$router.push('/');
                this.$message.warning(this.$t('mainMenu.logoutSuccess'));
            } catch(e) {
                this.$message.error(this.$t('mainMenu.logoutError'));
            } finally {
                this.logoutLoading = false;
            }
        }
    }
}
</script>
