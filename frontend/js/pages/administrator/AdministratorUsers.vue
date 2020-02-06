<template>
    <div class="ml20 mr20">
        <h2>{{$t('p.aUsers.title')}}</h2>
        <el-table :data="users" stripe v-loading="dataLoading" :empty-text="$t('p.aUsers.empty')">
            <el-table-column prop="id" :label="$t('p.aUsers.id')" align="center" header-align="center" width="80"></el-table-column>
            <el-table-column prop="name" :label="$t('p.aUsers.name')" header-align="center" align="center" width="300"></el-table-column>
            <el-table-column :label="$t('p.aUsers.email')" align="center" header-align="center" width="250">
                <template slot-scope="scope">
                    <span class="mr5">{{scope.row.email}}</span>
                    <span :style="scope.row.email_verified ? 'color:#67C23A' : 'color:#EB9E05'"><i :class="scope.row.email_verified ? 'el-icon-circle-check' : 'el-icon-close'"></i></span>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.aUsers.phone')" align="center" header-align="center" width="250">
                <template slot-scope="scope">
                    <span class="mr5">{{scope.row.phone}}</span>
                    <span v-if="scope.row.phone" :style="scope.row.is_phone_confirmed ? 'color:#67C23A' : 'color:#EB9E05'"><i :class="scope.row.is_phone_confirmed ? 'el-icon-circle-check' : 'el-icon-close'"></i></span>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.aUsers.date')" width="180" header-align="center" align="center">
                <template slot-scope="scope">
                    {{scope.row.created_at | formatDateTimeFromNow}}
                </template>
            </el-table-column>
            <el-table-column v-if="isAdmin" width="260">
                <template slot-scope="scope">
                    <el-popover placement="left" trigger="hover" :content="$t('p.aUsers.makeModer')">
                        <el-button slot="reference" v-on:click="grantRights(scope.row.id, 'MODERATOR')"
                                   :disabled="scope.row.rights && scope.row.rights.moder_rights"
                                   icon="el-icon-star-on" type="info" size="mini" circle plain>
                        </el-button>
                    </el-popover>
                    <el-popover placement="top" trigger="hover" :content="$t('p.aUsers.makeAdmin')">
                        <el-button v-on:click="grantRights(scope.row.id, 'ADMIN')"
                                   slot="reference" :disabled="scope.row.rights && scope.row.rights.admin_rights"
                                   icon="el-icon-d-caret" type="danger" size="mini" circle plain>
                        </el-button>
                    </el-popover>
                    <el-button v-on:click="revokeRights(scope.row.id)"
                               type="warning" size="mini" plain :disabled="!scope.row.rights || (!scope.row.rights.admin_rights && !scope.row.rights.moder_rights)">
                        {{$t('p.aUsers.revokeAll')}}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination @current-change="loadUsers" layout="total, prev, pager, next" class="alignC"
                       :current-page.sync="usersPaginator.currentPage"
                       :page-size="usersPaginator.pageSize"
                       :total="usersPaginator.total - 1">
        </el-pagination>
    </div>
</template>
<script>
import { mapState, mapGetters } from 'vuex'

export default{
    data(){
        return {
            dataLoading: false,
            users: [],
            usersPaginator: {currentPage: 1, pageSize: 10, total: 0},
        }
    },
    computed: {
        ...mapState(['system']),
        ...mapGetters(['isAdmin'])
    },
    async created() {
        this.dataLoading = true;
        await this.loadUsers();
        this.dataLoading = false;
    },
    methods: {
        async grantRights(id, rightsType) {
            let roleType = rightsType === 'ADMIN' ? this.$t('p.aUsers.admin') : this.$t('p.aUsers.moder');
            try {
                let result = await this.$confirm(this.$t('p.aUsers.grantRights', {type: roleType}), this.$t('p.common.warning'), {
                    confirmButtonText: this.$t('p.common.yes'), cancelButtonText: this.$t('p.common.cancelBtn'), type: 'warning'
                });
                if (await this.post('/administrator/grantRights', {'USER_ID': id, 'RIGHTS_TYPE': rightsType}))  {
                    this.$message.success(this.$t('p.aUsers.grantSuccess', {id: id, type: roleType}));
                    return this.loadUsers();
                }
            } catch (e) {
                // TODO Ничего не делаем?
            }
        },
        async revokeRights(id) {
            if (await this.post('/administrator/revokeRights', {'USER_ID': id})) {
                this.$message.success(this.$t('p.aUsers.revokeSuccess'));
                return this.loadUsers();
            }
        },
        async loadUsers() {
            this.dataLoading = true;
            let response = await this.get('/administrator/users', {params: this.usersPaginator});
            if (response.status !== 4) {
                this.users = response.data;
                this.usersPaginator.total = response.total;
            }
            this.dataLoading = false;
        }
    }
}
</script>
