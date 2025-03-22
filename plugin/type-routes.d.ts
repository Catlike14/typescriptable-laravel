// This file is auto generated by TypescriptableLaravel.
declare namespace App.Route {
  export type Name = 'api/user' | 'current-user-photo.destroy' | 'current-user.destroy' | 'dashboard' | 'download.show' | 'feeds.index' | 'feeds.show' | 'home' | 'login' | 'logout' | 'other-browser-sessions.destroy' | 'page.about' | 'page.p1pdd' | 'page.pqd2p' | 'page.subscribe' | 'password.confirm' | 'password.confirmation' | 'password.email' | 'password.request' | 'password.reset' | 'password.update' | 'podcasts.index' | 'podcasts.show' | 'posts.index' | 'posts.show' | 'profile.show' | 'rss.index' | 'rss.show' | 'sanctum.csrf-cookie' | 'submission.index' | 'submission.store' | 'two-factor-challenge' | 'two-factor.confirm' | 'two-factor.disable' | 'two-factor.enable' | 'two-factor.login' | 'two-factor.qr-code' | 'two-factor.recovery-codes' | 'two-factor.secret-key' | 'user-password.update' | 'user-profile-information.update' | 'user/confirm-password' | 'user/two-factor-recovery-codes'
  export type Path = '/' | '/a-propos' | '/api/user' | '/blog' | '/blog/{post_slug}' | '/contact' | '/dashboard' | '/download/{feed_slug}/{podcast_slug}' | '/feeds' | '/feeds/{feed_slug}' | '/forgot-password' | '/login' | '/logout' | '/p1pdd' | '/podcasts' | '/podcasts/{podcast_slug}' | '/pqd2p' | '/reset-password' | '/reset-password/{token}' | '/rss' | '/rss/{feed_slug}' | '/s-abonner' | '/sanctum/csrf-cookie' | '/two-factor-challenge' | '/user' | '/user/confirm-password' | '/user/confirmed-password-status' | '/user/confirmed-two-factor-authentication' | '/user/other-browser-sessions' | '/user/password' | '/user/profile' | '/user/profile-information' | '/user/profile-photo' | '/user/two-factor-authentication' | '/user/two-factor-qr-code' | '/user/two-factor-recovery-codes' | '/user/two-factor-secret-key'
  export interface Params {
    'login': never
    'logout': never
    'password.request': never
    'password.reset': {
      token?: App.Route.Param
    }
    'password.email': never
    'password.update': never
    'user-profile-information.update': never
    'user-password.update': never
    'user/confirm-password': never
    'password.confirmation': never
    'password.confirm': never
    'two-factor.login': never
    'two-factor-challenge': never
    'two-factor.enable': never
    'two-factor.confirm': never
    'two-factor.disable': never
    'two-factor.qr-code': never
    'two-factor.secret-key': never
    'two-factor.recovery-codes': never
    'user/two-factor-recovery-codes': never
    'profile.show': never
    'other-browser-sessions.destroy': never
    'current-user-photo.destroy': never
    'current-user.destroy': never
    'sanctum.csrf-cookie': never
    'download.show': {
      podcast_slug: App.Route.Param
    }
    'feeds.index': never
    'feeds.show': {
      feed_slug: App.Route.Param
    }
    'home': never
    'page.about': never
    'page.subscribe': never
    'page.p1pdd': never
    'page.pqd2p': never
    'podcasts.index': never
    'podcasts.show': {
      podcast_slug?: App.Route.Param
    }
    'posts.index': never
    'posts.show': {
      post_slug: App.Route.Param
    }
    'rss.index': never
    'rss.show': {
      feed_slug: App.Route.Param
    }
    'submission.index': never
    'submission.store': never
    'api/user': never
    'dashboard': never
  }

  export type Method = 'HEAD' | 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
  export type Param = string | number | boolean | undefined
  export interface Link { name: App.Route.Name, path: App.Route.Path, params?: App.Route.Params[App.Route.Name], methods: App.Route.Method[] }
  export interface RouteConfig<T extends App.Route.Name> {
    name: T
    params?: T extends keyof App.Route.Params ? App.Route.Params[T] : never
  }
}
