// Adapted from shadcn/ui button (fetched via shadcn MCP) for the BAIGR theme.
import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/utils";

const buttonVariants = cva(
  "inline-flex shrink-0 items-center justify-center gap-2 rounded-full font-medium whitespace-nowrap transition-all duration-300 outline-none focus-visible:outline-2 focus-visible:outline-iris focus-visible:outline-offset-3 disabled:pointer-events-none disabled:opacity-50",
  {
    variants: {
      variant: {
        default:
          "bg-lime text-ink hover:bg-iris-deep hover:text-white hover:-translate-y-0.5 hover:shadow-[0_16px_38px_rgba(114,107,214,0.4)]",
        glass: "glass text-ink hover:border-iris/50 hover:text-iris",
        ghost: "text-ink hover:text-iris",
      },
      size: {
        default: "h-11 px-6 text-sm",
        lg: "h-14 px-9 text-base",
        xl: "h-16 px-11 text-lg",
      },
    },
    defaultVariants: { variant: "default", size: "default" },
  }
);

function Button({
  className,
  variant = "default",
  size = "default",
  asChild = false,
  ...props
}: React.ComponentProps<"button"> &
  VariantProps<typeof buttonVariants> & { asChild?: boolean }) {
  if (asChild && React.isValidElement(props.children)) {
    const child = props.children as React.ReactElement<{ className?: string }>;
    return React.cloneElement(child, {
      className: cn(buttonVariants({ variant, size }), child.props.className, className),
    });
  }
  return (
    <button
      data-slot="button"
      className={cn(buttonVariants({ variant, size, className }))}
      {...props}
    />
  );
}

export { Button, buttonVariants };
