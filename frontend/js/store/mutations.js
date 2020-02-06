export default {
    // Работа с пользовательскими данными
    /**
     * Установить юзера
     * @param state    стейт
     * @param userInfo данные юзера
     */
    setUserInfo(state, userInfo) {
        if (userInfo.reservedLessons && userInfo.reservedLessons.length > 0) {
            userInfo.reservedLessons = _.sortBy(userInfo.reservedLessons, [o => { return moment(o.ldate + ' ' + o.ltime + ':00'); }]);
        }
        if (userInfo.goneLessons && userInfo.goneLessons.length > 0) {
            userInfo.goneLessons = _.sortBy(userInfo.goneLessons, [o => { return moment(o.ldate + ' ' + o.ltime + ':00'); }]);
        }
        state.user = userInfo;
    },
    logoutUser(state, details) {
        state.user = null;
    },
    setUserReservedLessons(state, reservedLessons) {
        state.user.reservedLessons = _.sortBy(reservedLessons, [o => { return moment(o.ldate + ' ' + o.ltime + ':00'); }]);
    },
    setUserGoneLessons(state, goneLessons) {
        state.user.goneLessons = _.sortBy(goneLessons, [o => { return moment(o.ldate + ' ' + o.ltime + ':00'); }]);
    },
    setUserAvatar(state, avatar) {
        state.user.user_details.photo_url = avatar;
    },
    setHasPassword(state, hasPassword) {
        if (state.user) {
            state.user.has_password = hasPassword;
        }
    },
    setPhoneConfirmation(state, isConfirm) {
        if (state.user) {
            state.user.is_phone_confirmed = isConfirm;
        }
    },
    setCategories(state, categories) {
        state.categories = categories;
    },
    setCountries(state, countries) {
        state.countries = countries;
    },
    setStatuses(state, statuses) {
        state.STATUSES = statuses;
    },
    addStates(state, data) {
        state.states[data.code] = data.states;
    },
    addCities(state, data) {
        state.cities[data.stateCode] = data.cities;
    },
    setAgings(state, agings) {
        state.agings = agings;
    },
    setAuditories(state, auditories) {
        state.auditories = auditories;
    },
    setSearchedLessons(state, lessons) {
        state.searchedLessons = lessons;
    },
    addSearchedLessons(state, lessons) {
        state.searchedLessons = state.searchedLessons.concat(lessons);
    },
    clearFilter(state) {
        state.pageFilter = {lessonName: null, cityName: null, pupils: null, CATS: [], selectAll: false, resultsOnPage: 10, plannedDate: null};
        state.searchedLessons = [];
    },
    setNews(state, value) {
        state.news = value;
    },
    setSystemProperties(state, value) {
        state.system = value;
    },
    initDataLoading(state, val) {
        state.initDataLoading = val;
    },
    setReserveStatuses(state, value) {
        state.reserveStatuses = value;
    },
    setWithdrawalStatuses(state, value) {
        state.withdrawalStatuses = value;
    },
    setRecommendations(state, value) {
        if (state.user) {
            state.user.recommendations = value;
        }
    }
}