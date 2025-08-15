<template>
  <div class="not-found">
    <h1>404</h1>
    <p>Oops! The page you're looking for doesn’t exist.</p>
    <router-link to="/">Go back to Home</router-link>
  </div>
</template>

<script setup>
// No script logic needed unless you want extra behavior
</script>

<style scoped>
.not-found {
  text-align: center;
  padding: 4rem 1rem;
}
.not-found h1 {
  font-size: 5rem;
  margin-bottom: 1rem;
  color: #d9534f;
}
.not-found p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
}
a {
  text-decoration: none;
  color: #007bff;
}
a:hover {
  text-decoration: underline;
}
</style>
