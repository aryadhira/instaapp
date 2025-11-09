import { ref } from "vue";

const tokenCookieName = "jwt_token"; 

export const useAuth = () => {
  const token = ref<string | null>(null);
  const user = ref<any>(null);

  // load token (SSR-aware)
  function loadToken() {
    const cookie = useCookie(tokenCookieName);
    const t =
      cookie.value ??
      (process.client ? localStorage.getItem(tokenCookieName) : null);
    token.value = t;
  }

  // set token (client or server)
  function setToken(t: string | null, httpOnly = false) {
    token.value = t;
    const cookie = useCookie(tokenCookieName);
    cookie.value = t; 
    if (process.client && t && !httpOnly) {
      localStorage.setItem(tokenCookieName, t);
    } else if (process.client && !t) {
      localStorage.removeItem(tokenCookieName);
    }
  }

  async function login(credentials: { identifier: string; password: string }) {
    const config = useRuntimeConfig()
    
    const apiUrl = config.public.baseUrl + "/login"
    console.log(apiUrl)
    try {
      const res = await $fetch(apiUrl, {
        method: "POST",
        body: credentials,
        credentials: "include",
        headers: { "Content-Type": "application/json" },
      });

      // If backend returns token in JSON as fallback:
      if ((res as any).status == "success") {
        setToken((res as any).token, false);
      } else {
        loadToken();
      }
      return true;
    } catch (err) {
      console.error("login error", err);
      throw err;
    }
  }

  function logout() {
    try {
      $fetch("/api/auth/logout", {
        method: "POST",
        credentials: "include",
      }).catch(() => {});
    } finally {
      setToken(null);
      user.value = null;
    }
  }

  function isAuthenticated() {
    return !!token.value;
  }

  loadToken();

  return { token, user, loadToken, setToken, login, logout, isAuthenticated };
};
