import React from "react";
import Link from "next/link";
import { logoutAction } from "@/app/actions/auth";
import { Calendar, LogOut, UserCheck } from "lucide-react";

interface NavbarProps {
  userName?: string;
}

export function Navbar({ userName = "Event Manager" }: NavbarProps) {
  return (
    <header className="sticky top-0 z-30 w-full border-b border-slate-200 bg-white/95 backdrop-blur shadow-xs">
      <div className="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex h-16 items-center justify-between">
          {/* Brand & Left Navigation */}
          <div className="flex items-center gap-8">
            <Link
              href="/dashboard/manager"
              className="flex items-center gap-2.5 font-bold text-slate-900 tracking-tight hover:opacity-90 transition-opacity"
            >
              <div className="w-9 h-9 rounded-lg bg-slate-900 text-white flex items-center justify-center shadow-xs">
                <Calendar className="w-5 h-5" />
              </div>
              <span className="text-base sm:text-lg">Sistem Event Management</span>
            </Link>

            <nav className="hidden md:flex items-center gap-1">
              <Link
                href="/dashboard/manager"
                className="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-900 bg-slate-100 hover:bg-slate-200 transition-colors"
              >
                Dashboard
              </Link>
            </nav>
          </div>

          {/* User Profile & Logout Action */}
          <div className="flex items-center gap-4">
            <div className="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200">
              <UserCheck className="w-4 h-4 text-emerald-600" />
              <div className="text-xs">
                <p className="font-semibold text-slate-800 leading-tight">{userName}</p>
                <p className="text-[11px] text-slate-500 font-medium leading-tight">
                  Event Manager
                </p>
              </div>
            </div>

            <form action={logoutAction}>
              <button
                type="submit"
                className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 rounded-lg transition-colors cursor-pointer"
                title="Keluar dari sistem"
              >
                <LogOut className="w-3.5 h-3.5" />
                <span>Logout</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </header>
  );
}
