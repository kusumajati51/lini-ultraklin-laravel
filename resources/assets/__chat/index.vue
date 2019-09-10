<template>
  <div>
    <vk-navbar>
      <vk-navbar-toggle slot="right" @click="show = true"></vk-navbar-toggle>
    </vk-navbar>
    <vk-offcanvas-content>
      <vk-offcanvas mode="push" stuck flipped :show.sync="show">
        <vk-offcanvas-close @click="show = false"></vk-offcanvas-close>
        <vk-tabs>
          <vk-tabs-item title="Messages">
            <messages-list></messages-list>
          </vk-tabs-item>
          <vk-tabs-item title="User List">
            <user-list></user-list>
          </vk-tabs-item>
        </vk-tabs>
      </vk-offcanvas>
    </vk-offcanvas-content>
  </div>
</template>

<script>
export default {
  name: 'Chat',
  data: () => ({
    show: false
  })
}
</script>
