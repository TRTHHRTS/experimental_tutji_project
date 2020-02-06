<template>
    <div v-if="user && isOperator">
        <table><tr>
            <td valign="top" style="width: 300px;">
                <el-menu :default-active="$route.path" active-text-color="#FF7100" router>
                    <el-menu-item index="/administrator/main">
                        <span>{{$t('p.a.main')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/administrator/users">
                        <span>{{$t('p.a.users')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/administrator/lessons">
                        <span>{{$t('p.a.lessons')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/administrator/reserves">
                        <span>{{$t('p.a.reserves')}}</span>
                    </el-menu-item>
                    <el-menu-item index="/administrator/withdrawals">
                        <span>Запросы выплат</span>
                    </el-menu-item>
                </el-menu>
            </td>
            <td valign="top" style="width: 100%; padding-left: 20px; padding-right: 20px;">
                <router-view></router-view>
            </td>
        </tr></table>
    </div>
</template>
<script>
import { mapState, mapGetters } from 'vuex'

export default {
    data(){
        return {}
    },
    computed: {
        ...mapState(['user']),
        ...mapGetters(['isOperator'])
    }
}
</script>
