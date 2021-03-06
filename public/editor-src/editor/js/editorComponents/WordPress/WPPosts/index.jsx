import React from "react";
import EditorComponent from "visual/editorComponents/EditorComponent";
import CustomCSS from "visual/component/CustomCSS";
import { WPShortcode } from "../common/WPShortcode";
import Toolbar from "visual/component/Toolbar";
import * as toolbarConfig from "./toolbar";
import defaultValue from "./defaultValue.json";
import { styleClassName, styleCSSVars } from "./styles";

const resizerPoints = ["centerLeft", "centerRight"];

class WPPosts extends EditorComponent {
  static get componentId() {
    return "WPPosts";
  }

  static defaultValue = defaultValue;

  handleResizerChange = patch => this.patchValue(patch);

  renderForEdit(v) {
    const attributes = {
      numberposts: v.numberPosts,
      category: v.category,
      orderby: v.orderBy,
      order: v.order,
      include: v.include,
      exclude: v.exclude,
      meta_key: v.metaKey,
      meta_value: v.metaValue,
      post_type: v.postType,
      post_status: v.postStatus
    };

    return (
      <Toolbar {...this.makeToolbarPropsFromConfig(toolbarConfig)}>
        <CustomCSS selectorName={this.getId()} css={v.customCSS}>
          <WPShortcode
            name="brizy_posts"
            attributes={attributes}
            placeholderIcon="wp-shortcode"
            placeholderContainerWidth={this.props.meta.desktopW}
            className={styleClassName(v)}
            style={styleCSSVars(v)}
            resizerPoints={resizerPoints}
            resizerMeta={this.props.meta}
            resizerValue={v}
            resizerOnChange={this.handleResizerChange}
          />
        </CustomCSS>
      </Toolbar>
    );
  }
}

export default WPPosts;
