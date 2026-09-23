"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { loginAction } from "@/app/actions/auth";
import { FeedbackAlert } from "@/components/FeedbackAlert";
import { Button } from "@/components/Button";
import { Calendar, Lock, Mail } from "lucide-react";

export default function LoginPage() {
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setError(null);
    setIsLoading(true);

    const formData = new FormData(e.currentTarget);
    const result = await loginAction(null, formData);

    if (result.error) {
      setError(result.error);
      setIsLoading(false);
    } else if (result.success) {
      router.push("/dashboard/manager");
      router.refresh();
    }
  }

  return (
    <div className="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div className="mx-auto w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-md">
          <Calendar className="w-6 h-6" />
        </div>
        <h1 className="mt-4 text-2xl font-bold tracking-tight text-slate-900">
          Sistem Event Management
        </h1>
        <p className="mt-1 text-sm text-slate-600">
          Portal Masuk Khusus Event Manager
        </p>
      </div>

      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div className="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-xl sm:px-10">
          <form onSubmit={handleSubmit} className="space-y-5">
            {error && (
              <FeedbackAlert
                type="error"
                message={error}
                onClose={() => setError(null)}
              />
            )}

            <div>
              <label
                htmlFor="email"
                className="block text-sm font-medium text-slate-700 mb-1"
              >
                Email
              </label>
              <div className="relative rounded-lg shadow-2xs">
                <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <Mail className="h-4 w-4 text-slate-400" />
                </div>
                <input
                  id="email"
                  name="email"
                  type="email"
                  autoComplete="email"
                  required
                  placeholder="email@domain.com"
                  className="block w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-colors"
                />
              </div>
            </div>

            <div>
              <label
                htmlFor="password"
                className="block text-sm font-medium text-slate-700 mb-1"
              >
                Password
              </label>
              <div className="relative rounded-lg shadow-2xs">
                <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <Lock className="h-4 w-4 text-slate-400" />
                </div>
                <input
                  id="password"
                  name="password"
                  type="password"
                  autoComplete="current-password"
                  required
                  placeholder="••••••••"
                  className="block w-full rounded-lg border border-slate-300 pl-10 pr-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-colors"
                />
              </div>
            </div>

            <Button
              type="submit"
              variant="primary"
              size="md"
              className="w-full mt-2"
              isLoading={isLoading}
            >
              Masuk ke Dashboard
            </Button>
          </form>


        </div>
      </div>
    </div>
  );
}
