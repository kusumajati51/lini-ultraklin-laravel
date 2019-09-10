<template>
  <div class="uk-card uk-card-default">
    <div class="uk-card-body">
      <ul class="uk-nav uk-nav-default" uk-nav>
        <template v-for="(m, key) in menu">
          <li v-if="m.divider" :key="key" class="uk-nav-divider"></li>
          <li
            v-else-if="m.header"
            :key="key"
            class="uk-nav-header uk-margin-remove"
          >
            {{ m.header }}
          </li>
          <li v-else-if="m.item" :key="key">
            <router-link
              v-if="m.item.link_js != null"
              :to="{ name: m.item.link_js }"
            >
              {{ m.item.title }}
            </router-link>
            <a v-else :href="m.item.link">{{ m.item.title }}</a>
          </li>
        </template>

        <li class="uk-nav-divider"></li>
        <li><a :href="logoutUrl">Sign Out</a></li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    menu: {
      required: true
    }
  },

  data() {
    return {
      logoutUrl: `${document
        .querySelector('meta[name="base-url"')
        .getAttribute('content')}/admin/logout`
    }
  }
}
</script>
