"use client";

import React, { useEffect } from "react";
import { Button } from "@/components/Button";
import { AlertTriangle } from "lucide-react";

export default function DashboardError({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    console.error("Dashboard caught error:", error);
  }, [error]);

  return (
    <div className="flex flex-col items-center justify-center p-12 text-center bg-white rounded-xl border border-slate-200 shadow-sm max-w-lg mx-auto mt-12">
      <div className="w-12 h-12 mb-4 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
        <AlertTriangle className="w-6 h-6" />
      </div>
      <h2 className="text-lg font-bold text-slate-900">
        Terjadi Kesalahan pada Dashboard
      </h2>
      <p className="mt-2 text-sm text-slate-600">
        Maaf, sistem mengalami kendala saat memuat data. Silakan coba muat ulang halaman.
      </p>
      <div className="mt-6">
        <Button onClick={() => reset()} variant="primary" size="md">
          Coba Lagi
        </Button>
      </div>
    </div>
  );
}
