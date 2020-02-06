<template>
    <div class="ml20 mr20">
        <h2>{{$t('p.aLessons.title')}}</h2>
        <el-table :data="lessons" stripe v-loading="dataLoading" :empty-text="$t('p.aLessons.empty')">
            <el-table-column prop="id" :label="$t('p.aLessons.id')" align="center" header-align="center" width="80"></el-table-column>
            <el-table-column prop="user.teacher_name" :label="$t('p.aLessons.userName')" align="center" header-align="center" width="150"></el-table-column>
            <el-table-column prop="lesson_name" :label="$t('p.aLessons.lessonName')"></el-table-column>
            <el-table-column :label="$t('p.aLessons.date')" width="180" header-align="center" align="center">
                <template slot-scope="scope">
                    {{scope.row.created_at | formatDateTimeFromNow}}
                </template>
            </el-table-column>
            <el-table-column v-if="isOperator" align="center" width="80">
                <template slot-scope="scope">
                    <el-popover placement="left" trigger="hover" :content="scope.row.is_unique ? $t('p.aLessons.cancelUnique') : $t('p.aLessons.makeUnique')">
                        <el-button slot="reference" v-on:click="setUnique(scope.row.id, scope.row.is_unique)"
                                   :icon="scope.row.is_unique ? 'el-icon-star-on' : 'el-icon-star-off'" type="success" size="mini" circle plain>
                        </el-button>
                    </el-popover>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination @current-change="loadLessons" layout="total, prev, pager, next" class="alignC"
                       :current-page.sync="lessonsPaginator.currentPage"
                       :page-size="lessonsPaginator.pageSize"
                       :total="lessonsPaginator.total">
        </el-pagination>
    </div>
</template>
<script>
import {mapGetters} from 'vuex'

export default{
    data(){
        return {
            dataLoading: false,
            lessons: [],
            lessonsPaginator: {currentPage: 1, pageSize: 10, total: 0}
        }
    },
    computed: mapGetters(['isOperator']),
    async created() {
        this.dataLoading = true;
        await this.loadLessons();
        this.dataLoading = false;
    },
    methods: {
        async setUnique(id, isUnique) {
            const response = await this.post('/administrator/setUnique', {'LESSON_ID': id, 'IS_UNIQUE': !isUnique});
            if (response.status === 4) {
                this.$message.error(this.$t('p.aLessons.uniqueError', {id: id}));
            } else {
                this.$message.success(this.$t('p.aLessons.uniqueSuccess'));
                return this.loadLessons();
            }
        },
        async loadLessons() {
            this.dataLoading = true;
            let response = await this.get('/administrator/lessons', {params: this.lessonsPaginator});
            if (response.status !== 4) {
                this.lessons = response.data;
                this.lessonsPaginator.total = response.total;
            }
            this.dataLoading = false;
        }
    }
}
</script>
