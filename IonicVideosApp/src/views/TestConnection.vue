<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Test Connection</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content>
      <ion-button @click="testConnection">Test Connection</ion-button>
      <p v-if="response">{{ response }}</p>
    </ion-content>
  </ion-page>
</template>

<script>
import api from '@/plugins/axios';

export default {
  data() {
    return {
      response: '',
    };
  },
  methods: {
    async testConnection() {
      try {
        const res = await api.get('/multimedia');
        this.response = 'Connected successfully: ' + JSON.stringify(res.data);
      } catch (error) {
        this.response = 'Connection failed: ' + error.message;
      }
    },
  },
};
</script>