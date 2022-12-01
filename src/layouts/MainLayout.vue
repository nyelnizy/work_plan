<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="toggleLeftDrawer" />

        <q-toolbar-title>
          <q-avatar>
            <img
              src="https://lh3.googleusercontent.com/a-/AOh14GhowCWks8QqVWOx5Xizzhq26mxSFJgBetIvUzRk=s360-p-rw-no"
            />
          </q-avatar>
          Lazy Daniel
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered>
      <q-list bordered padding class="rounded-borders text-primary">
        <q-item
          clickable
          v-ripple
          :active="link === 'work'"
          @click="goTo('work')"
          active-class="my-menu-link"
        >
          <q-item-section avatar>
            <q-icon name="spoke" />
          </q-item-section>

          <q-item-section>Submit Work</q-item-section>
        </q-item>
        <q-item
          clickable
          v-ripple
          :active="link === 'invoice'"
          @click="goTo('invoice')"
          active-class="my-menu-link"
        >
          <q-item-section avatar>
            <q-icon name="spoke" />
          </q-item-section>

          <q-item-section>Generate Invoice</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view v-slot="{ Component }">
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref } from "vue";

export default {
  setup() {
    return {
      link: ref("work"),
    };
  },
  methods: {
    goTo(route) {
      this.link = route;
      this.$router.push({ name: route });
    },
  },
};
</script>
