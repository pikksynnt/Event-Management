import React from "react";
import { CheckCircle2, AlertCircle, AlertTriangle, Info, X } from "lucide-react";

interface FeedbackAlertProps {
  type?: "success" | "error" | "warning" | "info";
  message: string;
  onClose?: () => void;
  className?: string;
}

export function FeedbackAlert({
  type = "info",
  message,
  onClose,
  className = "",
}: FeedbackAlertProps) {
  if (!message) return null;

  const styles = {
    success: {
      container: "bg-emerald-50 border-emerald-200 text-emerald-800",
      icon: <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0" />,
    },
    error: {
      container: "bg-rose-50 border-rose-200 text-rose-800",
      icon: <AlertCircle className="w-5 h-5 text-rose-600 shrink-0" />,
    },
    warning: {
      container: "bg-amber-50 border-amber-200 text-amber-800",
      icon: <AlertTriangle className="w-5 h-5 text-amber-600 shrink-0" />,
    },
    info: {
      container: "bg-slate-50 border-slate-200 text-slate-800",
      icon: <Info className="w-5 h-5 text-slate-600 shrink-0" />,
    },
  };

  const current = styles[type];

  return (
    <div
      role="alert"
      className={`flex items-start justify-between gap-3 p-4 rounded-xl border text-sm shadow-sm ${current.container} ${className}`}
    >
      <div className="flex items-start gap-3">
        {current.icon}
        <span className="leading-snug">{message}</span>
      </div>
      {onClose && (
        <button
          onClick={onClose}
          type="button"
          aria-label="Tutup notifikasi"
          className="text-slate-400 hover:text-slate-600 rounded-lg p-0.5"
        >
          <X className="w-4 h-4" />
        </button>
      )}
    </div>
  );
}
