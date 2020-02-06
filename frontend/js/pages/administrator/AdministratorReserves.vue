<template>
    <div class="ml20 mr20">
        <h2>{{$t('p.aReserves.title')}}</h2>
        <el-checkbox v-model="paginatorReserves.showClosed" @change="loadReserves">{{$t('p.aReserves.showClosed')}}</el-checkbox>
        <el-table :data="reserves" stripe v-loading="dataLoading" :empty-text="$t('p.aReserves.empty')">
            <el-table-column type="expand">
                <template slot-scope="scope">
                    <p>{{$t('p.aReserves.info')}}  <strong>{{scope.row.lesson_name}}</strong></p>
                    <p>{{$t('p.aReserves.info2', {name: scope.row.lesson.user.name, phone: $filters.formatPhone(scope.row.teacher_phone)})}}</p>
                    <p>{{$t('p.aReserves.info3', {name: scope.row.user.name, phone: $filters.formatPhone(scope.row.user.phone)})}}</p>
                    <div class="mb10">
                        <span @click="getHistory(scope.row.id)" class="internal_link">{{$t('p.aReserves.messagesHistory')}}</span>
                    </div>
                    <p v-if="scope.row.closed">Причина закрытия: <strong>{{scope.row.reason}}</strong></p>
                    <template v-if="!scope.row.closed">
                        <el-button @click="askUser(42, scope.row.id)" type="info" size="mini" plain>
                            {{$t('p.aReserves.askQteacher')}}
                        </el-button>
                        <el-button class="ml10" @click="askUser(43, scope.row.id)" type="info" size="mini" plain>
                            {{$t('p.aReserves.askQpupil')}}
                        </el-button>
                        <el-button class="ml10" @click="openCloseReserveDlg(scope.row.id, scope.row.user_id)" type="warning" size="mini" plain>{{$t('p.aReserves.closeReserve')}}</el-button>
                    </template>
                </template>
            </el-table-column>
            <el-table-column prop="count" :label="$t('p.aReserves.count')" align="center" header-align="center" width="105"></el-table-column>
            <el-table-column align="center" header-align="center" width="40">
                <template slot-scope="scope">
                    <i v-if="scope.row.closed" class="el-icon-remove"></i>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.aReserves.status')">
                <template slot-scope="scope">
                    <i class="el-icon-bell"></i>
                    <span>{{reserveStatuses[scope.row.reserve_status]}}</span>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.aReserves.date')">
                <template slot-scope="scope">
                    <span>{{scope.row.reserve_time.lesson_date|formatDate}}{{$t('p.aReserves.inSuffix')}}{{scope.row.reserve_time.lesson_time}} ({{scope.row.reserve_time.duration|formatDuration}})</span>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination @current-change="loadReserves" layout="total, prev, pager, next" class="alignC"
                       :current-page.sync="paginatorReserves.currentPage"
                       :page-size="paginatorReserves.pageSize"
                       :total="paginatorReserves.total">
        </el-pagination>
        <el-dialog :title="$t('p.aReserves.dTitle')" :visible.sync="historyDialogVisible">
            <el-table :data="conversationHistory" v-loading="historyLoading">
                <el-table-column label="Кто">
                    <template slot-scope="scope">
                        <template v-if="scope.row.user.rights.moder_rights || scope.row.user.rights.admin_rights">
                            Администрация (ID={{scope.row.user.id}})
                        </template>
                        <template v-else>
                            {{scope.row.user.name}}
                        </template>
                    </template>
                </el-table-column>
                <el-table-column prop="message" label="Сообщение" header-align="center"></el-table-column>
                <el-table-column label="Дата" align="center" header-align="center" width="200">
                    <template slot-scope="scope">
                        <span>{{scope.row.updated_at|formatDateTime}}</span>
                    </template>
                </el-table-column>
            </el-table>
        </el-dialog>
        <el-dialog title="Закрытие записи" :visible.sync="closeReserveDlgVisible" width="400px">
            <el-form :model="closeDlgForm">
                <el-form-item label="Причина закрытия" label-width="400">
                    <el-input v-model="closeDlgForm.message" auto-complete="off"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="closeReserveDlgVisible=false">Отмена</el-button>
                <el-button type="primary" @click="closeReserve">Отправить</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<script>
export default {
    data() {
        return {
            dataLoading: false,
            reserves: [],

            historyDialogVisible: false,
            conversationHistory: [],
            historyLoading: false,
            paginatorReserves: {currentPage: 1, pageSize: 10, total: 0, showClosed: false},

            closeReserveDlgVisible: false,
            closeDlgForm: {}
        }
    },
    computed: mapState(['reserveStatuses']),
    async created() {
        this.dataLoading = true;
        await this.loadReserves();
        this.dataLoading = false;
    },
    methods: {
        async loadReserves() {
            try {
                this.dataLoading = true;
                let response = await this.get('/administrator/reserves', {params: this.paginatorReserves});
                if (response.status !== 4) {
                    this.reserves = response.data;
                    this.paginatorReserves.total = response.total;
                }
            } finally {
                this.dataLoading = false;
            }
        },
        async askUser(newStatus, reserveId) {
            try {
                let msg = await this.$prompt(this.$t('p.aReserves.typeQuestion'), this.$t('p.aReserves.warning'),
                    {confirmButtonText: 'Готово', cancelButtonText: 'Отмена',});
                let response = await this.post('/reserves/adminAnswer', {newStatus: newStatus, reserveId: reserveId, message: msg.value});
                if (response.status === 0) {
                    this.$message.success(this.$t('p.aReserves.questionSaved'));
                    await this.loadReserves();
                }
            } catch(e) {
            }
        },
        async openCloseReserveDlg(reserveId, userId) {
            this.closeDlgForm = {
                reserveId: reserveId,
                userId: userId,
                moneyBack: false,
                message: "",
                timeId: null
            };
            this.closeReserveDlgVisible = true;
            this.$forceUpdate();
        },
        async closeReserve() {
            try {
                let response = await this.post('/reserves/close', this.closeDlgForm);
                if (response.status === 0) {
                    this.$message.success(this.$t('p.aReserves.reserveClosed'));
                    await this.loadReserves();
                }
            } catch(e) {
            } finally {
                this.closeReserveDlgVisible = false;
            }
        },
        async getHistory(id) {
            try {
                this.historyLoading = true;
                this.conversationHistory = [];
                this.historyDialogVisible = true;
                this.conversationHistory = await this.get('/reserves/getHistory', {params: {reserveId: id}});
                console.log(JSON.stringify(this.conversationHistory));
            } finally {
                this.historyLoading = false;
            }
        }
    }
}
</script>
