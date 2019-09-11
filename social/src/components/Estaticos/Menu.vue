<template>
  <nav>
    <div class="nav-wrapper">
      <router-link  :to="url" class="brand-logo center">Logo</router-link>
      <ul class="right hide-on-med-and-down">
        <slot></slot>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Menu',
  props:[ 'url' ],
  data() {
    return {

    }
  },
}
</script>
