<template>
  <p class="clearfix mb-0">
    <span class="float-sm-left  d-block d-md-inline-block mt-25">
      COPYRIGHT  © {{ new Date().getFullYear() }}
      <b-link
        class="ml-25"
        href="https://capitalsoftware.com.ni/"
        target="_blank"
      >CSOFT</b-link>
      <span class="d-none d-sm-inline-block">, All rights Reserved</span>
    </span>

  </p>
</template>

<script>
import { BLink } from 'bootstrap-vue'

export default {
  components: {
    BLink,
  },
}
</script>
