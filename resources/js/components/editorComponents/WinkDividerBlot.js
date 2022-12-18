import Quill from 'quill';

let BlockEmbed = Quill.import('blots/block/embed');

class BlogDividerBlot extends BlockEmbed {
}

BlogDividerBlot.blotName = 'divider';
BlogDividerBlot.tagName = 'hr';

export default BlogDividerBlot;
