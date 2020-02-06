const LessonEditor = () => import('./pages/LessonEditor.vue');
const Filter = () => import('./pages/Filter.vue');
const Administrator = () => import('./pages/administrator/Administrator.vue');
const AdministratorMain = () => import('./pages/administrator/AdministratorMain.vue');
const AdministratorUsers = () => import('./pages/administrator/AdministratorUsers.vue');
const AdministratorLessons = () => import('./pages/administrator/AdministratorLessons.vue');
const AdministratorReserves = () => import('./pages/administrator/AdministratorReserves.vue');
const Messages = () => import('./pages/Messages.vue');
const Help = () => import('./pages/policies/Help.vue');
const Register = () => import('./pages/Register.vue');
const Profile = () => import('./pages/profilePages/Profile.vue');
const LessonView = () => import('./pages/LessonView.vue');
const MessagesChannel = () => import('./pages/MessagesChannel.vue');
const ServicePolicy = () => import('./pages/policies/ServicePolicy.vue');
const PrivacyPolicy = () => import('./pages/policies/PrivacyPolicy.vue');
const News = () => import('./pages/News.vue');
const Faq = () => import('./pages/Faq.vue');
const Feedback = () => import('./pages/policies/Feedback.vue');
const ConfirmationProcess = () => import('./pages/ConfirmationProcess.vue');
const PasswordReset = () => import('./pages/PasswordReset.vue');
const ErrorPage = () => import('./pages/Error.vue');
const ProfileInfo = () => import('./pages/profilePages/ProfileInfo.vue');
const ProfilePhoneConfirmation = () => import('./pages/profilePages/ProfilePhoneConfirmation.vue');
const ProfileLessons = () => import('./pages/profilePages/ProfileLessons.vue');
const ProfileYourLessons = () => import('./pages/profilePages/ProfileYourLessons.vue');
const ProfileHistory = () => import('./pages/profilePages/ProfileHistory.vue');
const ProfileSettings = () => import('./pages/profilePages/ProfileSettings.vue');
const ProfileStatistic = () => import('./pages/profilePages/ProfileStatistic.vue');
const PersonalDataAgreement = () => import('./pages/policies/PersonalDataAgreement.vue');
const Cookie = () => import('./pages/policies/Cookie.vue');
const Home = () => import('./pages/Home.vue');
const Login = () => import('./pages/Login.vue');

export default [
    {path: '/', redirect: {name: 'home'}},
    {path: '/home', component: Home, name: 'home'},
    {path: '/filter', component: Filter, name: 'filter',
        children: [
            {path: 'userDialog'}
        ]
    },
    {path: '/login', component: Login, name: 'login'},
    {path: '/administrator', component: Administrator, redirect: '/administrator/main', name: 'admin',
        children: [
            {path: 'main', component: AdministratorMain},
            {path: 'users', component: AdministratorUsers},
            {path: 'lessons', component: AdministratorLessons},
            {path: 'reserves', component: AdministratorReserves}
        ]
    },
    {path: '/register', component: Register, name: 'register'},
    {path: '/profile', component: Profile, redirect: '/profile/info', name: 'profile',
        children: [
            {path: 'info', component: ProfileInfo, name: 'profileInfo'},
            {path: 'plan', component: ProfileLessons},
            {path: 'lessons', component: ProfileYourLessons},
            {path: 'history', component: ProfileHistory},
            {path: 'settings', component: ProfileSettings},
            {path: 'statistic', component: ProfileStatistic},
            {path: 'phoneConfirmation', component: ProfilePhoneConfirmation}
        ]
    },
    {path: '/reset/:token', component: PasswordReset, props: true},
    {path: '/lesson/:lessonId', component: LessonView, name: 'view'},
    {path: '/edit/:id', component: LessonEditor, name: 'editor'},
    {path: '/messages', component: Messages, name: 'messages'},
    {path: '/messages/:rcptId', component: MessagesChannel, name: 'messagesChannel'},

    {path: '/news', component: News, name: 'news'},
    {path: '/help', component: Help, name: 'help'},
    {path: '/faq', component: Faq, name: 'faq'},
    {path: '/feedback', component: Feedback, name: 'feedback'},
    {path: '/confirmationProcess', component: ConfirmationProcess, name: 'confirmationProcess'},
    {path: '/privacyPolicy', component: PrivacyPolicy, name: 'privacyPolicy'},
    {path: '/servicePolicy', component: ServicePolicy, name: 'servicePolicy'},
    {path: '/dataAgreement', component: PersonalDataAgreement},
    {path: '/cookiePolicy', component: Cookie},

    {path: '*', component: ErrorPage},
];