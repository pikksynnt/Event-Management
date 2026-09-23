import React from "react";
import { LucideIcon } from "lucide-react";

interface StatCardProps {
  title: string;
  value: number;
  icon: LucideIcon;
  variant?: "default" | "amber" | "emerald" | "rose";
  description?: string;
}

export function StatCard({
  title,
  value,
  icon: Icon,
  variant = "default",
  description,
}: StatCardProps) {
  const variantStyles = {
    default: {
      iconBg: "bg-slate-100 text-slate-700",
      accent: "text-slate-900",
    },
    amber: {
      iconBg: "bg-amber-100 text-amber-700",
      accent: "text-amber-900",
    },
    emerald: {
      iconBg: "bg-emerald-100 text-emerald-700",
      accent: "text-emerald-900",
    },
    rose: {
      iconBg: "bg-rose-100 text-rose-700",
      accent: "text-rose-900",
    },
  };

  const currentVariant = variantStyles[variant];

  return (
    <div className="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow transition-shadow">
      <div className="flex items-center justify-between">
        <span className="text-sm font-medium text-slate-600">{title}</span>
        <div className={`p-2.5 rounded-lg ${currentVariant.iconBg}`}>
          <Icon className="w-5 h-5" />
        </div>
      </div>
      <div className="mt-4">
        <div className="text-3xl font-bold tracking-tight text-slate-900">
          {value.toLocaleString()}
        </div>
        {description && (
          <p className="mt-1 text-xs text-slate-500">{description}</p>
        )}
      </div>
    </div>
  );
}
