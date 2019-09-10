<template>
  <vk-table
    hoverable
    :divided="false"
    :data="users"
    row-selectable
    :selected-rows.sync="selectedUser"
  >
    <vk-table-column headless cell="name"></vk-table-column>
  </vk-table>
</template>

<script>
export default {
  name: 'UserList',
  data: () => ({
    users: [
      { name: 'John A. Smith' },
      { name: 'Joan B. Jones' },
      { name: 'Bob C. Uncle' }
    ],
    selectedUser: []
  }),
  methods: {
    getUsers() {
      this.users = []
      let db = this.$firebase.database()
      db.ref('users').on('value', snap => {
        snap.forEach(val => {
          let obj = val.val()
          obj.id = val.key
          this.users.push(obj)
        })
      })
    }
  },
  mounted() {
    this.getUsers()
  }
}
</script>
