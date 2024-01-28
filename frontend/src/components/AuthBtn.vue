<template>
    <div>
        <component :is="'script'" class="amocrm_oauth" charset="utf-8" :data-client-id="VITE_CLIENT_ID"
            data-title="Установить интеграцию" data-compact="false" data-class-name="className" data-color="default"
            data-error-callback="handleOauthError"
            src="https://www.amocrm.ru/auth/button.min.js">
            
        </component>
    </div>
</template>

<script setup>
    const VITE_CLIENT_ID = import.meta.env.VITE_CLIENT_ID;        
</script>