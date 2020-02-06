<template>
    <div class="w900 mAuto">
        <h2 class="alignC">{{$t('p.help.title')}}</h2>
        <p>{{$t('p.help.info1')}}</p>
        <i18n path="p.help.info2" tag="p">
            <router-link to="/faq" place="faq">{{$t('p.help.faq')}}</router-link>
            <router-link to="/feedback" place="feedback">{{$t('p.help.feedback')}}</router-link>
        </i18n>
        <i18n path="p.help.info3" tag="p">
            <router-link to="/confirmationProcess" place="confirm">{{$t('p.help.confirmProcess')}}</router-link>
        </i18n>
        <i18n path="p.help.info4" tag="p">
            <router-link to="/privacyPolicy" place="policy">{{$t('p.help.privacyPolicy')}}</router-link>
        </i18n>
        <h3>Контакты</h3>
        <p><strong>TUTJI</strong></p>
        <p>Author: TRTHHRTS</p>
    </div>
</template>
<script>
export default {
    data(){ return {} },
    computed: mapState(['system'])
}
</script>
