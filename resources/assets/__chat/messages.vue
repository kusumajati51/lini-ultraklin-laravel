<template>
  <div>
    <vk-grid
      v-for="message in messagesList"
      :key="message.id"
      class="uk-child-width-auto@s uk-flex-right uk-text-left"
    >
      <div>
        <vk-card>{{ message.text }}</vk-card>
      </div>
    </vk-grid>
    <div class="message-input uk-child-width-auto@s uk-flex">
      <div><textarea class="uk-textarea"></textarea></div>
      <div class="uk-padding-small uk-padding-remove-vertical">
        <vk-icon-button icon="mail"></vk-icon-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Messages',
  data: () => ({
    messagesList: [
      { id: 1, text: 'hey tayo' },
      { id: 2, text: 'hey tayo' },
      {
        id: 3,
        text:
          'dia bis kecil ramai, melaju, mengebut. dia bis kecil ramai, melaju, mengebut. dia bis kecil ramai, melaju, mengebut. '
      }
    ]
  }),
  methods: {
    getUsers() {
      let db = this.$firebase.database()
      db.ref('users').on('value', snap => {
        this.userList = snap.val()
      })
    }
  },
  mounted() {
    // console.log(this.$firebase)
  }
}
</script>

<style>
.message-input {
  bottom: 10px;
  position: fixed;
}
</style>
