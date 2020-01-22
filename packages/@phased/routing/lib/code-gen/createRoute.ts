import { Route } from "@phased/routing";

export const createRoute = () => {
  return (route: Route): string => {
    // If default child is exists, the route should not have a name.
    const routeName =
      route.children && route.children.some(m => m.file_path === "")
        ? ""
        : `name: '${route.name}'`;

    const routePath = `path: '${route.uri}'`;

    const routeBeforeEnter = `beforeEnter: phaseBeforeEnter`;

    const routeComponent = `component: ${route.componentName}`;

    const routeMeta = !route.middleware
      ? ""
      : `meta: { middleware: ${JSON.stringify(route.middleware.split(","))} }`;

    const routeChildren = !route.children
      ? ""
      : `children: [${route.children.map(createRoute()).join(",")}]`;

    return `    {
        ${[
          routeName,
          routePath,
          routeBeforeEnter,
          routeComponent,
          routeMeta,
          routeChildren
        ]
          .filter(a => a)
          .join(",\n        ")}
    }`;
  };
};
