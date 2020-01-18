import { Route } from 'phase';

export const createImport = (dynamic: boolean = false, chunkNamePrefix: string = ''): ((args: Route) => string) => {
  return (route: Route): string => {
    const preparedImport = createImport(dynamic, chunkNamePrefix);
    const { name, prefix, componentName, file_path } = route;
    const webpackChunkName = `${chunkNamePrefix}${prefix}`;
    const webpackComment = webpackChunkName ? `/* webpackChunkName: "${webpackChunkName}" */ ` : ''
    const code = dynamic
      ? `const ${componentName} = () => import(${webpackComment}'../../../${file_path}')`
      : `import ${componentName} from '../../../${file_path}'`;

    return route.children
      ? [code].concat(route.children.map(preparedImport)).join("\n")
      : code;
  };
}
