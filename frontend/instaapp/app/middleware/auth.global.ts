export default defineNuxtRouteMiddleware((to, from) => {
  const auth = useAuth();

  // Public pages
  if (to.meta.auth === false) return;

  // If not logged in, redirect to login
  if (!auth.isAuthenticated()) {
    return navigateTo("/login");
  }

  // Prevent logged-in user from visiting /login again
  if (to.path === "/login" && auth.isAuthenticated()) {
    return navigateTo("/");
  }
});
