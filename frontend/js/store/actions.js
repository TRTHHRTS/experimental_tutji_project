export default  {
    /**
     * Загружает записи о занятиях
     * @param context         коммит
     * @param vm              инстанс vue
     * @param asTeacher       признак загрузки записей для учителя или для ученика
     * @param paginatorParams параметры пагинатора
     * @param isNew           признак получения новых занятий
     * @returns {Promise<Array>} список записей
     */
    async loadCommonReserves(context, [vm, asTeacher, paginatorParams, isNew]) {
        let params = {
            asTeacher: asTeacher,
            isNew: !!isNew
        };
        if (paginatorParams) {
            params = {...paginatorParams, ...params};
        }
        let response = await vm.get('/api/profile/commonReserves', {params: params});
        if (response.status !== 4) {
            response.data = _.sortBy(response.data, [o => {return moment(o.ldate + ' ' + o.ltime + ':00');}]);
            return response;
        }
        vm.$message.error(response.message);
    },
    /**
     * Отменяет запись
     * @param context   коммит
     * @param vm        инстанс vue
     * @param reserveId идентификатор записи
     * @param isTeacher признак того, что запрос делает преподаватель
     * @param cancelAll признак того, отменить все записи на связанное занятия, или нет
     */
    async commonCancelRecord(context, [vm, reserveId, isTeacher, cancelAll]) {
        try {
            let question = cancelAll ? vm.$t("p.vuex.cancelAllText") : vm.$t("p.vuex.cancelText");
            await vm.$confirm(question, vm.$t("p.vuex.question"), {
                confirmButtonText: vm.$t("p.common.yes"), cancelButtonText: vm.$t("p.common.no"), type: "warning"
            });
            let params = {
                isTeacher: isTeacher,
                reserveId: reserveId,
                cancelAll: cancelAll
            };
            let response = await vm.post('/reserves/cancelReserve', params);
            if (response.status === 0) {
                vm.$message.success(cancelAll ? vm.$t("p.vuex.allRecordsCanceled") : vm.$t("p.vuex.recordCanceled"));
            } else if (response.status === 4) {
                throw new Error(response.message);
            }
        } catch (e) {
            // cancel - если пользователь нажал "Отмена" при подтверждении действия
            if ("cancel" !== e) {
                vm.$alert(e.message, vm.$t('p.common.error'), {type: "error"});
            }
        }
    },
}