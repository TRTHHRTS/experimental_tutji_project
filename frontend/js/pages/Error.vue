<template>
    <div>
        <div class="w300 mAuto">
            <h2>404</h2>
            <h3>{{$t('p.error404.title')}}</h3>
            <p>{{$t('p.error404.subtitle')}}</p>
            <div class="alignR">
                <router-link to="/">{{$t('p.error404.goBack')}}</router-link>
            </div>
        </div>
    </div>
</template>