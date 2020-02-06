export default {
    getUserInfo: state => state.user,
    isUser: state => state.user !== null,
    count: state => state.count,
    isAdmin: state => {
        let user = state.user;
        return user && user.rights && user.rights.admin_rights;
    },
    isModer: state => {
        let user = state.user;
        return user && user.rights && user.rights.moder_rights;
    },
    isOperator: state => {
        let user = state.user;
        return user && user.rights && (user.rights.moder_rights || user.rights.admin_rights);
    },
    getUserGender: state => {
        if (!state.user) {
            return null;
        }
        if (!state.user.user_details) {
            return null;
        }
        if (state.user.user_details.gender === 1) {
            return 'Мужской';
        } else if (state.user.user_details.gender === 2) {
            return 'Женский';
        } else {
            return 'Не задано';
        }
    }
}