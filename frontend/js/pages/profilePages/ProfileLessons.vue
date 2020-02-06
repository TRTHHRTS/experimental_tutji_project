<template>
    <div>
        <h3 class="alignC">{{$t('p.pLessons.title')}}</h3>
        <el-table :data="reserves" stripe class="mb10 mAuto" v-loading="reservesLoading"
                  :empty-text="$t('p.pLessons.empty')">
            <el-table-column :label="$t('p.pLessons.resCount')" width="150" align="center" header-align="center">
                <template slot-scope="scope">
                    <span>{{$t('p.pLessons.resCount2', {count: scope.row.count})}}</span>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pLessons.date')" width="200" align="center" header-align="center">
                <template slot-scope="scope">
                    <i class="el-icon-date"></i>
                    <span>{{scope.row.ltime}} {{scope.row.ldate|formatDate}}</span>
                    <!--<span>{{$t('p.pLessons.date2', {date: scope.row.ldate, time: scope.row.ltime})}}</span>-->
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pLessons.status')" width="350" align="center" header-align="center">
                <template slot-scope="scope">
                    <i class="el-icon-bell"></i>
                    <span>{{reserveStatuses[scope.row.reserve_status]}}</span>
                    <el-button v-on:click="cancelRecord(scope.row.rlid)" type="danger" size="mini" plain round>{{$t('p.pLessons.cancel')}}</el-button>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pLessons.lessonName')" min-width="350" align="center" header-align="center">
                <template slot-scope="scope">
                    <strong><span class="btn_link" v-on:click="$router.push('/lesson/'+scope.row.lesson_id)">{{scope.row.name}}</span></strong>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pLessons.duration')" width="150" align="center" header-align="center">
                <template slot-scope="scope">
                    <span>{{scope.row.duration | formatDuration}}</span>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
export default {
    data(){
        return {
            reserves: [],
            reservesLoading: true,
        }
    },
    computed: mapState(['reserveStatuses']),
    methods: {
        ...mapActions(['loadCommonReserves', 'commonCancelRecord']),
        async cancelRecord(reserveId) {
            try {
                this.reservesLoading = true;
                await this.commonCancelRecord([this, reserveId, false, false]);
                this.reserves = (await this.loadCommonReserves([this, false, null, true])).data;
            } finally {
                this.reservesLoading = false;
            }
        }
    },
    async created() {
        document.title = this.$t('p.pLessons.pageTitle');
    },
    async mounted() {
        try {
            this.reserves = (await this.loadCommonReserves([this, false, null, true])).data;
        } finally {
            this.reservesLoading = false;
        }
    }
}
</script>