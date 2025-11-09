export default defineNuxtPlugin((nuxtApp) => {
  const apiFetch = async (path: string, opts: RequestInit = {}) => {
    const { token } = useAuth();
    const runtime = useRuntimeConfig();
    const base = runtime.public.baseUrl || ""; // set in nuxt.config

    const headers = (
      opts.headers ? { ...(opts.headers as Record<string, string>) } : {}
    ) as Record<string, string>;

    if (token.value) {
      headers["Authorization"] = `Bearer ${token.value}`;
    }

    const res = await $fetch(base + path, {
      ...opts,
      headers,
      // include credentials so cookies get sent for cookie-based auth
      credentials: "include",
    } as any);

    return res;
  };

  return {
    provide: {
      apiFetch,
    },
  };
});
